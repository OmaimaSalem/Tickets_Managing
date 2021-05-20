<template>
<div class="modal fade" id="CreateItemCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ $t('Category.createCategory') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form @submit.prevent="createCategory" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">{{ $t('Category.name') }}</label>
                        <input type="text" name="name" class="form-control" v-model="form.name">
                    </div>
                    <div class="form-group">
                        <label for="description">{{ $t('Category.description') }}</label>
                        <input type="text" name="description" class="form-control" v-model="form.description">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ $t('Category.create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
</template>
<script>
export default {
    props: ['form'],
    methods: {
        createCategory() {
            this.$Progress.start();
            this.$store
                .dispatch("item/createCategory", this.form)
                .then(response => {
                    $("#CreateItemCategory").modal("hide");
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                })
                .catch(error => {
                    console.log(error);
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
    }
}
</script>
