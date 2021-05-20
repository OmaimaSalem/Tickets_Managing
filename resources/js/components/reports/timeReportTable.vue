<template>
  <div class="card mt-2">
    <div class="card-body table-responsive p-0">
      <table class="table table-hover table-sm table-bordered">
        <thead>
          <tr>
            <th width="10%">{{$t('Time.date')}}</th>
            <th width="10%">{{$t('Time.client')}}</th>
            <th width="20%">{{$t('Time.projectName')}}</th>
            <th width="20%">{{$t('Time.timeCounting')}}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in time_count.data" :key="item.id">
            <td>{{ item.the_day | DateOnly}}</td>
            <td>{{ item.owner_name }}</td>
            <td>{{ item.project_name }}</td>
            <td>{{ item.time_counting }}</td>
          </tr>
          <tr >
            <td colspan="2" style="background: #f4f6f9;"></td>
            <td v-if="total" class="text-right" style="background: #f4f6f9;"><h4>{{$t('Time.total')}}:</h4></td>
            <td v-if="total"><h4>{{ total }}</h4></td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
</template>

<script>
export default {
  props: {
    time_count: {
      type: Object,
      required: true
    },
    total: {
      type: String
    }
  }
};
</script>

<style>
</style>
