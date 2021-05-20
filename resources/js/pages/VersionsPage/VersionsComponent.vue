<template lang="">
    <div>
  <h4>{{$t('AttributesEmails.Versions')}}</h4>

                <i
                  @click="createVersionModal"
                  data-widget="collapse"
                  data-toggle="tooltip"
                  title="Delete"
                  style="font-size: .8rem"
                  class="text-success icon pl-1 fas fa-plus fa-xs float-right"
                ></i>


  <v-simple-table v-if="versions && versions.length > 0">
    <template v-slot:default>
      <thead>
        <tr>
          <th class="text-left">
            {{$t('AttributesEmails.Version')}}
          </th>
          <th class="text-left">
           {{$t('AttributesEmails.Download')}} 
          </th>
          <th class="text-left">
           {{$t('AttributesEmails.Actions')}} 
          </th>
        </tr>
      </thead>
      <tbody>

        <tr v-for="version in versions">
          <td>{{version.version}}</td>
          <td>{{version.url}}</td>
          <td style="display: inline-flex;margin-top: 1.1rem">
                <i
                  @click="editVersion(version)"
                  data-widget="collapse"
                  data-toggle="tooltip"
                  title="Edit"
                  class="text-success icon fas fa-edit fa-xs"
                  style="
                    :hover {
                      color: #ffffff;
                    };
                    font-size: .8rem
                  "
                ></i>
                <i
                  @click="deleteVersion(version.id)"
                  data-widget="collapse"
                  data-toggle="tooltip"
                  title="Delete"
                  style="font-size: .8rem"
                  class="text-danger icon pl-1 fas fa-trash fa-xs"
                ></i>
              </td>
        </tr>

      </tbody>
    </template>
  </v-simple-table>

        <!-- Edit Version Modal -->
        <div class="modal fade" id="VersionModal" tabindex="-1" role="dialog" aria-labelledby="VersionModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="VersionModalLabel">{{$t('AttributesEmails.Modaltitle')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form>
                  <div class="form-group">
                    <label for="version"  class="col-form-label"> {{$t('AttributesEmails.Version')}}:</label>
                    <input type="text" v-model="form.version" class="form-control" id="version">
                    <has-error :form="form" field="version"></has-error>
                  </div>

                  <input type="file" @change="select">


                  <div class="form-group">
                    <label for="url" class="col-form-label">URL:</label>
                    <input type="text" v-model="form.url" class="form-control" id="url">
                    <has-error :form="form" field="url"></has-error>
                  </div>

                  <progress style="width:100%;" :value="progress" :max="max" variant="success"></progress>
                  <div v-if="max > 0">
                    {{ formatProgress((progress/max) * 100)}} %
                  </div>

                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{$t('AttributesEmails.close')}}</button>
                <button v-if="form.id" @click="updateVersion" type="button" class="btn btn-primary">{{$t('AttributesEmails.saveChanges')}}</button>
                <button v-if="!form.id" @click="createVersion" type="button" class="btn btn-primary">{{$t('AttributesEmails.save')}}</button>
              </div>
            </div>
          </div>
        </div>



    </div>
</template>
<script>

import { mapGetters } from 'vuex';

export default {
    data(){
        return {
          version: null,
          form: new Form({
            id:null,
            version: null,
            url:null,
          }),
          file: null,
          chunks: [],
          uploaded: 0,
          max: 0,
          prog: 0,
        }
    },
    methods: {
        formatProgress(number){
          return Math.ceil(number);
        },
        getVersions(){
            this.$store.dispatch('version/getVersions');
        },
        deleteVersion(id){
        Swal.fire({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!",
        }).then((result) => {
          if (result.value) {
            this.$store.dispatch('version/deleteVersion',id);
          }});
      },
      createVersionModal(){
        this.form.clear();
        $('#VersionModal').modal('show');
      },
      createVersion(){
        this.$store.dispatch('version/createVersion',this.form).then(data => {
          this.form.reset();
          $('#VersionModal').modal('hide');
        }).catch(errors => {
          if(errors.response){
            this.form.errors.errors = errors.response.data.errors;
          }
        });
      },
      editVersion(version){
        this.form.fill(version);
        $('#VersionModal').modal('show');
      },
      updateVersion(){
        this.$store.dispatch('version/updateVersion',this.form).then(data => {
          this.form.reset();
          $('#VersionModal').modal('hide');
        }).catch(errors => {
          if(errors.response){
            this.form.errors.errors = errors.response.data.errors;
          }
        });
      },



      // upload functions


            select(event) {
                this.file = event.target.files.item(0);
                this.prog = 0;
                this.createChunks();
            },
            upload() {
                axios(this.config).then(response => {
                  if(response.data.done == true){
                    this.prog = 0;
                    this.form.url = response.data.url;
                  }else {
                    this.chunks.shift();
                    this.prog++;
                  }
                }).catch(error => {});
            },
            createChunks() {
                let size = 100000, chunks = Math.ceil(this.file.size / size);
                for (let i = 0; i < chunks; i++) {
                    this.chunks.push(this.file.slice(
                        i * size, Math.min(i * size + size, this.file.size), this.file.type
                    ));
                }
                this.max = chunks;
            }









    },
    mounted(){
        this.getVersions();
    },
    watch: {
    chunks(n, o) {
        if (o.length > 0) {
            this.upload();
          }
        },
    },

    computed: {
        ...mapGetters({
            versions : "version/getVersions"
        }),


            progress() {
                return this.prog;
            },
            formData() {
                let formData = new FormData;

                formData.set('is_last', this.chunks.length === 1);
                formData.set('file', this.chunks[0], `${this.file.name}.part`);

                return formData;
            },
            config() {
                return {
                    method: 'POST',
                    data: this.formData,
                    url: '/v-api/versions/upload',
                    headers: {
                        'Content-Type': 'application/octet-stream'
                    },
                    onUploadProgress: event => {
                        this.uploaded += event.loaded;
                    }
                };
            }


    }
}
</script>
<style lang="">
    
</style>