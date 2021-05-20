<script>
  export default {
    methods: {
      $can(permissionName) {
        return Permissions.indexOf(permissionName) !== -1;
      },
      $is(roleName) {
        return Roles.indexOf(roleName) !== -1;
      },
    },
  };
</script>
