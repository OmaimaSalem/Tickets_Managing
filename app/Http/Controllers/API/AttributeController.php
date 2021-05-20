<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\UserAttribute;
use App\Models\AttributeEmail;
use App\Http\Resources\Attribute\AttributeCollection;
use App\Http\Resources\Attribute\AttributeResource;
use Carbon\Carbon;
use App\Http\Requests\Attribute\EmailRequest;

class AttributeController extends BaseController
{

    public function __construct()
    {
        $this->middleware('permission:attribute-emails-list', ['only' => ['get_attributes_emails']]);
        $this->middleware('permission:attribute-emails-create', ['only' => ['create_attribute_email']]);
        $this->middleware('permission:attribute-emails-edit', ['only' => ['edit_attribute_email']]);
        $this->middleware('permission:attribute-emails-delete', ['only' => ['delete_attribute_email']]);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user_type)
    {

        if($user_type == 'all'){
            $attributes = Attribute::paginate();
        }else {
            $attributes = Attribute::where('user_type',$user_type)->paginate();
        }
        return $this->sendResponse(new AttributeCollection($attributes), 'Attaribute retrieved successfully.');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attribute = [
            'user_type' => $request->user_type,
            'name' => $request->name,
            'type' => $request->type,
            'values' => $request->values,
        ];
        $attaribute = Attribute::create( $attribute);
        return $this->sendResponse(new AttributeResource($attaribute), 'Attaribute retrieved successfully.');

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attribute $attribute)
    {
        $attribute_ar = [
            'user_type' => $request->user_type,
            'name' => $request->name,
            'type' => $request->type,
            'values' => $request->values,
        ];
        $attribute->update($attribute_ar);
        return $this->sendResponse(new AttributeResource($attribute), 'Attaribute updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return $this->sendResponse($attribute->id, 'Attaribute updated successfully.');
    }


    public function users_attributes() {
        $attributes = Attribute::where('user_type','employee')->paginate(0,['id','type','name','values']);
        return $this->sendResponse(new AttributeCollection($attributes), 'Attaribute retrieved successfully.');
    }


    public function clients_attributes() {
        $attributes = Attribute::where('user_type','client')->paginate(0,['id','type','name','values']);
        return $this->sendResponse(new AttributeCollection($attributes), 'Attaribute retrieved successfully.');
    }

    public function save_user_attributes(Request $request) {
//        return $request->all();
        $user = \App\Models\User::find($request['user_id']);

        if(!$user){
            return $this->sendError(['success'=>false], 'Errors');
        }
        $attributes_collection = collect($request['attributes']);
        $attributes = $attributes_collection->map(function($attribute)  {
            return [
                'attribute_id' => $attribute['attribute_id'],
                'value' =>  $attribute[$attribute['type']],
                'type'  =>  $attribute['type'],
            ];
        });
        $user->attributes()->delete();
        $user->attributes()->createMany($attributes->all());
        return $this->sendResponse(new UserResource($user), 'Attributes saved successfully.');
    }

    public function merge_request($request) {
        $carbon = Carbon::parse($request->mail_start);
        switch($request->email_time) {
            case "daily":
                $request->merge(['next_run_time' => $request->mail_start]);
            break;
            case "weekly":
                $request->merge(['next_run_time' => $carbon->addWeek()]);
            break;
            case "last_day_in_the_month":
                $request->merge(['next_run_time' =>  $carbon->endOfMonth()]);
            break;

            case "every_15_day":
                $request->merge(['next_run_time' => $carbon->addMonthNoOverflow()]);
            break;

            case "every_year":
                $request->merge(['next_run_time' => $carbon->addYear()]);
            break;
            case "every_three_months":
                $request->merge(['next_run_time' => $carbon->addMonths(3)]);
            break;
            case "every_six_months":
                $request->merge(['next_run_time' => $carbon->addMonths(6)]);
            break;

            case "specific_date":
                $request->merge(['next_run_time' => $request->mail_start]);
            break;
            default:
            $request->merge(['next_run_time' => $request->mail_start]);
        }
    }

    public function create_attribute_email(EmailRequest $request) {
        $this->merge_request($request);
        $request->merge(['attribute_value' => is_array($request->attribute_value) ? json_encode($request->attribute_value) : $request->attribute_value]);
        $attribute = AttributeEmail::updateOrCreate([
            'user_type' => $request->user_type,
            'attribute_id' => $request->attribute_id,
            'attribute_value' => $request->attribute_value,
            'email_time' => $request->email_time,
        ],$request->all());
        return $this->sendResponse($attribute, 'Attaributes emails saved successfully.');
    }


    public function get_attributes_emails(Request $request){
        $attribute_emails = AttributeEmail::query();
        $query_params = json_decode($request->queryParams);



        foreach ($query_params->filters as $filter => $value) {
            if($value->name == "email_time") {
                $value->text = str_replace(" ","_",strtolower($value->text));
            }
            $attribute_emails->where($value->name,'like','%'.$value->text.'%');
        }

        foreach ($query_params->sort as $filter => $value) {
            $attribute_emails->orderBy($value->name,$value->order);
        }


        $attribute_emails = $attribute_emails->with('attribute')->latest()->paginate();
        return $this->sendResponse($attribute_emails, 'Attaributes emails saved successfully.');
    }

    public function edit_attribute_email(EmailRequest $request,$id){
        $attribute_email = AttributeEmail::find($id);
        $this->merge_request($request);
        $request->merge(['attribute_value' => is_array($request->attribute_value) ? json_encode($request->attribute_value) : $request->attribute_value]);

        if(!$attribute_email){
            return $this->sendError(['success'=>false], 'Errors');
        }
        $attribute_email->update($request->all());
        return $this->sendResponse($attribute_email, 'Attaributes emails updated successfully.');
    }


    public function delete_attribute_email($id) {
        $attribute_email = AttributeEmail::find($id);
        if(!$attribute_email){
            return $this->sendError(['success'=>false], 'Errors');
        }
        $attribute_email->delete();
        return $this->sendResponse($id, 'Attaributes emails deleted successfully.');

    }
}
