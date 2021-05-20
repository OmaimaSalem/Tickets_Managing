<template>
  <div class="">
    <div class="error-page">
        <h2 class="headline text-warning"> 404</h2>

        <div class="error-content">
          <h3><i class="fas fa-exclamation-triangle text-warning"></i>{{$t('Partial.oops')}}</h3>

          <p>
            {{$t('Partial.notFind')}}<router-link to="/admin/dashboard">{{$t('Partial.return')}}</router-link>{{$t('Partial.search')}}
          </p>

        </div>
        <!-- /.error-content -->
      </div>
  </div>
</template>

<script>
export default {

}
</script>

<style>

</style>
