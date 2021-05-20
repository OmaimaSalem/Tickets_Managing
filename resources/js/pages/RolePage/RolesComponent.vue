<template>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">{{$t('Roles.rolesTable')}}</h3>

          <div class="card-tools">
            <button type="submit" class="btn btn-success btn-sm" @click="newModel">
              <i class="fas fa-plus fa-fw"></i>
              <span class="d-none d-lg-inline">{{$t('Roles.newRole')}}</span>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          <table class="table table-hover">
            <thead>
              <tr>
                <th width="10">ID</th>
                <th width="20%">Name</th>
                <th width="60%">{{$t('Roles.permissions')}}</th>
                <th>{{$t('Roles.action')}}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="role in roles.data" :key="role.id">
                <td>{{ role.id }}</td>
                <td>{{ role.name }}</td>
                <td>
                  <span
                    v-for="item in role.permissions"
                    :key="item.id"
                    class="badge badge-danger mr-1"
                  >{{ item.name }}</span>
                </td>
                <td>
                  <a href="#" @click="editModel(role)" class="btn btn-info btn-sm">
                    <i class="fas fa-edit fa-fw"></i>
                  </a>
                  <a href="#" @click="deleteRole(role.id)" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash fa-fw"></i>
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="card-footer clear-fix">
          <pagination
            align="right"
            size="small"
            :show-disabled="true"
            :data="roles"
            :limit="3"
            @pagination-change-page="getResults"
          ></pagination>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="newRole"
      tabindex="-1"
      role="dialog"
      aria-labelledby="newRoleLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 v-show="!editMode" class="modal-title" id="newRoleLabel">{{$t('Roles.createNewRole')}}</h5>
            <h5 v-show="editMode" class="modal-title" id="newRoleLabel">{{$t('Roles.editRole')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form
            @submit.prevent="editMode ? editRole(form.id) : createRole()"
            @keydown="form.onKeydown($event)"
          >
            <div class="modal-body">
              <div class="form-group">
                <label for="name">{{$t('Roles.roleName')}}</label>
                <input
                  v-model="form.name"
                  type="text"
                  name="name"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('name') }"
                />
                <has-error :form="form" field="name"></has-error>
              </div>
              <div class="form-group">
                <div class="box-group" id="accordion">
                  <div class="panel box box-primary">
                    <div class="box-header with-border">
                      <p class="box-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" style="color: #000;">
                          {{$t('Roles.permissions')}}
                        </a>
                      </p>
                    </div>


                    <div id="collapseOne" class="panel-collapse collapse in">
                      <div class="box-body">
                        <div class="row">


                            <ul class="checktree">
                                <li class="" v-for="(permissions_list,key,index) in permissions" :key="index">
                                    <!-- <input :id="key" type="checkbox" />--><label :for="key">{{key}}</label>
                                    <ul>
                                        <li v-for="(permission) in permissions_list" :key="permission">
                                            <input type="checkbox" :id="permission" :value="permission" v-model="form.permissions" />
                                            <label :for="permission">{{permission}}</label>
                                        </li>
                                    </ul>
                                </li>
                            </ul>






                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <has-error :form="form" field="permissions"></has-error>
              </div>
            </div>

            <div class="modal-footer">
              <button v-show="!editMode" type="submit" class="btn btn-primary">{{$t('Roles.save')}}</button>
              <button v-show="editMode" type="submit" class="btn btn-success">{{$t('Roles.update')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script type="application/javascript" src="http://127.0.0.1:8000/js/checktree.js"></script>

<script>
import rolesApi from '../../api/roles';
import permissionsApi from '../../api/permissions';
const $ = require('jquery');
window.$ = $;

export default {
  data() {
    return {
      editMode: false,
      roles: {},
      form: new Form({
        id: "",
        name: "",
        permissions: [],
        selected: null
      }),
      selectedPermissions: [],
      selected: null,
      permissions: []
    };
  },
  methods: {
    getResults(page = 1) {
      this.$Progress.start();
      rolesApi
        .get({ page: page })
        .then(response => {
          this.roles = response.data.data;
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    newModel() {
      this.editMode = false;
      this.form.reset();
      $("#newRole").modal("show");
    },
    editModel(role) {
      this.editMode = true;
      this.form.reset();
      $("#newRole").modal("show");
      this.form.fill(role);
      this.form.permissions = [];
      role.permissions.forEach(permission => {
        this.form.permissions.push(permission.name);
      });
      this.form.selected = _.map(this.form.permissions, function(value, key) {
        return value.name;
      });
    },
    getPermissions() {
      permissionsApi
        .getAll()
        .then(response => {
          this.permissions = response.data.data;
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    createRole() {
      this.$Progress.start();
      this.form
        .post("/v-api/roles")
        .then(response => {
          $("#newRole").modal("hide");
          this.$Progress.finish();
          this.getResults();
          Toast.fire({
            type: "success",
            title: response.data.message
          });
        })
        .catch(error => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message
          });
        });
    },
    editRole(id) {
      this.$Progress.start();
      this.form
        .put("/v-api/roles/" + id)
        .then(response => {
          $("#newRole").modal("hide");
          this.$Progress.finish();
          this.getResults();
          Toast.fire({
            type: "success",
            title: response.data.message
          });
        })
        .catch(error => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message
          });
        });
    },
    deleteRole(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        if (result.value) {
          this.$Progress.start();
          rolesApi
            .delete(id)
            .then(response => {
              this.$Progress.finish();
              this.getResults();
              Toast.fire({
                type: "success",
                title: response.data.message
              });
            })
            .catch(error => {
              this.$Progress.fail();
              Toast.fire({
                type: "error",
                title: "can't delete the role"
              });
            });
        }
      });
    }
  },
  mounted() {
    this.getResults();
    this.getPermissions();

    (function($){
        $.fn.checktree = function(){
            $(':checkbox').on('click', function (event){
                event.stopPropagation();
                var clk_checkbox = $(this),
                chk_state = clk_checkbox.is(':checked'),
                parent_li = clk_checkbox.closest('li'),
                parent_uls = parent_li.parents('ul');
                parent_li.find(':checkbox').prop('checked', chk_state);
                parent_uls.each(function(){
                    parent_ul = $(this);
                    parent_state = (parent_ul.find(':checkbox').length == parent_ul.find(':checked').length);
                    parent_ul.siblings(':checkbox').prop('checked', parent_state);
                        });
                    });
                };



    }(jQuery));

      setTimeout(function(){
         $("ul.checktree").checktree();
      },7000);

  }
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>


<style scoped>
ul {
    list-style-type: none;
    margin: 3px;
    padding-left: 40px;
}

ul.checktree li:before {
    height: 1em;
    width: 12px;
    border-bottom: 1px dashed;
    content: "";
    display: inline-block;
    top: -0.3em;
}

ul.checktree li {
    border-left: 1px dashed;
}

ul.checktree li:last-child:before {
    border-left: 1px dashed;
}

ul.checktree li:last-child {
    border-left: none;
}

.item_input {
    width: 150px;
}

.item_area {
    width: 340px;
}

.item_qty {
    width: 80px;
}

</style>
