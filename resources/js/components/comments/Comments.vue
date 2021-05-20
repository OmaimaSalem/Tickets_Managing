<template>
  <div class="post">
    <div class="user-block">
      <img class="img-circle img-bordered-sm" src="/../../assets/img/profile.png" alt="user image" />
      <span class="username">
        <router-link :to="'/admin/profile/' + comment.created_by.id" v-if="comment.created_by">{{ comment.created_by.name }}</router-link>
      </span>
      <span class="description">{{ comment.created_at | DateWithTime}}</span>
      <span v-if="comment.send_mail" class="badge bg-success">{{ $t('Comments.replySent') }}</span>
      <span v-else-if="comment.by_client" class="badge bg-warning">{{ $t('Comments.commnetByClient') }}</span>
      <span v-else class="badge bg-danger">{{ $t('Comments.comment') }}</span>
    </div>
    <!-- /.user-block -->
    <p v-html="comment.comment">
    </p>
  </div>
</template>

<script>
export default {
    props: {
        comment: {
            type:Object
        }
    }
};
</script>

<style scpoed>
.post .user-block {
  margin-bottom: 15px;
  width: 100%;
}
.user-block {
  float: left;
}
.post {
    border-bottom: 1px solid #adb5bd;
    color: #666;
    margin-bottom: 15px;
    padding-bottom: 15px;
}
</style>
