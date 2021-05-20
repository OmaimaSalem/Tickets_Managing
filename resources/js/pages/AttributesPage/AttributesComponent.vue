<template>
  <div>
    <!-- wrapper -->
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" @click="newAttribute()">Add new attribute</button>

    <div class="form-group">
      <label for="user_type">user type</label>
      <select @change="select_user_type" v-model="attribute.user_type" class="form-control" id="user_type">
        <option value="all">All</option>
        <option value="client">Client</option>
        <option value="employee">Employee</option>
      </select>
    </div>

    <!-- Modal -->
    <div
      class="modal fade"
      id="AttributeModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="user_type">User type</label>
                <select v-model="
                attribute.user_type" class="form-control" id="user_type">
                  <option value="client">Client</option>
                  <option value="employee">Employee</option>
                </select>
              </div>

              <div class="form-group">
                <label for="exampleFormControlInput1">Name</label>
                <input type="text" class="form-control" v-model="attribute.name" id="name" />
              </div>

              <div class="form-group">
                <label for="attribute_type">Attribute type</label>
                <select v-model="attribute.type" class="form-control" id="attribute_type">
                  <option value="text">Text</option>
                  <option value="dropdown">Dropdown</option>
                  <option value="checkbox">Checkbox</option>
                  <option value="radiobutton">Radiobutton</option>
                </select>
              </div>

              <div class="form-group" v-if="attribute.type != 'text'">
                <label for="attribute_type">Attribute values</label>

                <div class="input-group">
                  <input
                    type="text"
                    class="form-control"
                    id="value"
                    @change="newAttributeValue($event)"/>
                  <div class="input-group-btn">
                    <div class="btn btn-default" type="submit"
                    >
                      <i class="fa fa-check" aria-hidden="true"></i>
                    </div>
                  </div>
                </div>

                <ul class="list-group list-group-flush">
                  <li
                    v-for="(value,index) in attribute.values"
                    :key="index"
                    class="list-group-item">
                    {{ value }}
                    <span class="badge badge-primary badge-pill float-right">
                      <i
                        @click="deleteAttributeValue(index)"
                        class="fa fa-times"
                        aria-hidden="true"
                      ></i>
                    </span>
                  </li>
                </ul>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            <button
              v-if="!editing"
              type="button"
              class="btn btn-primary"
              @click="saveAttribute"
            >Save changes</button>

            <button
              v-if="editing"
              type="button"
              class="btn btn-primary"
              @click="updateAttribute"
            >Update changes</button>
          </div>
        </div>
      </div>
    </div>
    <!-- end of modal -->

    <div v-if="attributes && attributes.length > 0">
      <vue-bootstrap4-table v-if="attributes" :rows="attributes" :columns="columns">
        <template
          slot="values"
          slot-scope="props"
        >{{ props.row.values ? props.row.values.join(',') : '' }}</template>

        <template slot="actions" slot-scope="props">
          <a href="javascript:;" @click="editAttribute(props.row)" class="btn btn-primary btn-xs">
            <i class="far fa-edit"></i>
            Edit
          </a>
          <a href="javascript:;" @click="deleteAttribute(props.row.id)" class="btn btn-danger btn-xs">
            <i class="fas fa-trash-alt"></i>
            Delete
          </a>
        </template>
      </vue-bootstrap4-table>
    </div>
  </div>
  <!-- wrapper -->
</template>

<script>
import { mapGetters } from "vuex";
import VueBootstrap4Table from "vue-bootstrap4-table";

export default {
  components: {
    VueBootstrap4Table
  },
  data() {
    return {
      attribute: { values: [],
            user_type: this.$route.params.type,
       },
      editing: false,
      columns: [
        {
          label: "user type",
          name: "user_type",
          filter: {
            type: "select",
            options: [
              {
                name: "Client",
                value: "client"
              },
              {
                name: "Employee",
                value: "employee"
              }
            ]
          },

          sort: true,
          row_text_alignment: "text-center"
        },
        {
          label: "type",
          name: "type",
          filter: {
            type: "simple"
          },
          sort: true,
          row_text_alignment: "text-left"
        },
        {
          label: "Name",
          name: "name",
          filter: {
            type: "simple"
          },

          sort: true,
          row_text_alignment: "text-center"
        },
        {
          label: "Values",
          name: "values",
          filter: {
            type: "simple"
          },
          sort: true
        },
        {
          label: "Actions",
          name: "actions"
        }
      ]
    };
  },
  methods: {
    newAttributeValue(event) {
      let val = event.target.value;
      if (!val) {
        console.log("first value"+ val);
        return false;
      }
      this.attribute.values.push(val);
      val = "";
    },
    deleteAttributeValue(index) {
      this.attribute.values.splice(index, 1);
    },
    newAttribute() {
      this.editing = false;
      this.attribute = { values: [] };
      $("#AttributeModal").modal("show");
    },
    saveAttribute() {
      console.log(this.attribute);
      this.$Progress.start();
      this.$store
        .dispatch("attribute/addAttribute", this.attribute)
        .then(resoponse => {
          $("#AttributeModal").modal("hide");
          this.attribute = { values: [] };
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    getAttributes() {
      this.$Progress.start();
      this.$store
        .dispatch("attribute/getAttributes", this.attribute.user_type)
        .then(resoponse => {
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    select_user_type() {
      this.getAttributes();
    },
    editAttribute(attribute) {
      this.attribute = Object.freeze(attribute);
      $("#AttributeModal").modal("show");
      this.editing = true;
    },
    updateAttribute() {
      this.$Progress.start();
      this.$store
        .dispatch("attribute/editAttribute", this.attribute)
        .then(resoponse => {
          $("#AttributeModal").modal("hide");
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    deleteAttribute($id) {
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
          this.$store
            .dispatch("attribute/deleteAttribute", $id)
            .then(resoponse => {
              this.$Progress.finish();
            })
            .catch(error => {
              this.$Progress.fail();
            });
        }
      });
    }
  },
  computed: {
    ...mapGetters({
      attributes: "attribute/attributesGetter"
    })
  },
  mounted() {
    this.getAttributes();
  }
};
</script>

<style>
</style>
