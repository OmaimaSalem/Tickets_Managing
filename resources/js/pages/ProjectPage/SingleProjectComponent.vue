<template>
  <div class="row justify-content-center">
      <vue-headful
          :title="'Project: '+project.name"
      />
      <v-container>
          <v-row style="margin-top: -1.8rem">
              <v-col cols="12">
                  <v-row>
                      <v-col cols="4" v-if="project.name">
                          <h2 class="project-name" :title="project.name" style="font-weight: bold">{{project.name | subProject}}</h2>
                      </v-col>
                      <v-col cols="5" style="margin-left: -1rem" v-if="project.status">
                          <div :id="project.status.name" class="status_container">
                              <span class="stat-name">{{project.status.name}}</span>
                          </div>
                      </v-col>
                      <v-col cols="3" style="display: flex;margin-left:1rem">
                          <div class="actions-container">
                              <router-link :to="{ name: 'project.copy', params: {id: project.id }}"><i  class='fas fa-copy'></i></router-link>
                          </div>
                          <div class="actions-container">
                              <router-link :to="{ name: 'project.discussion', params: {id: project.id }}"><i class="fas fa-comment-alt"></i></router-link>
                          </div>
                          <div class="actions-container">
                              <router-link :to="{name:'project.calender',params: {id: project.id}}">  <i class="fas fa-calendar-alt"></i></router-link>
                          </div>
                          <div class="actions-container">
                              <router-link :to="{name:'project.folder',params: {id: project.id}}"><i  class="fas fa-folder-open action"></i></router-link>
                          </div>
                          <div class="actions-container">
                              <router-link :to="{ name: 'project.edit', params: {id: project.id }}"><i  class="fas fa-edit"></i></router-link>
                          </div>
                          <div class="actions-container">
                              <a href="#" @click="deleteProject(project.id)"><i  class="fas fa-trash"></i></a>
                          </div>
                          <div class="actions-container">
                              <a href="#" v-if="!favorite"  @click="addToFav(project.id)"><i class="far fa-star"></i></a>
                              <a href="#" v-else  @click="removeFav(project.id)"><i class="fas fa-star"></i></a>
                          </div>
                      </v-col>
                  </v-row>

              </v-col>
          </v-row>
          <v-card style="border-radius: 15px;margin-top: -1.4rem">
              <v-container>
                  <v-row>
                      <v-col cols="12" v-if="project.description" style="display:flex;">
                          <span style="font-weight: bold;font-size: .9rem">{{$t("Project.desc")}}: </span>
                          <p class="text-muted ml-1" v-html="project.description"></p>
                      </v-col>
                  </v-row>
                  <v-row style="margin-top:-1.8rem">
                      <v-col cols="3">
                          <span style="font-weight: bold;font-size: .9rem">{{$t("Project.createdAt")}}: </span>
                          <small style="color:#959595;">{{project.created_at  | projectsDateNow}}</small>
                      </v-col>
                      <v-col cols="3">
                          <span style="font-weight: bold;font-size: .9rem">{{$t("Project.estimatedTime")}}: </span>
                          <small style="color:#959595;">{{project.estimated_time}} hours</small>
                      </v-col>
                      <v-col cols="3" v-if="project.owners" style="display: inline-flex;">
                          <span style="font-weight: bold;font-size: .9rem">{{$t("Project.clients")}}:</span>
                          <avatar
                              data-toggle="tooltip" data-placement="top" :title="project.owners[0].name"
                              color="#90b0fa"
                              v-if="project.owners[0]"
                              :fullname="project.owners[0].name"
                              :size="20"
                              style="margin-left: .3rem"
                          ></avatar>
                          <avatar
                              data-toggle="tooltip" data-placement="top" :title="project.owners[1].name"
                              color="#90b0fa"
                              v-if="project.owners[1]"
                              :fullname="project.owners[1].name"
                              :size="20"
                              style="margin-left: .3rem"
                          ></avatar>
                          <avatar
                              data-toggle="tooltip" data-placement="top" :title="project.owners[2].name"
                              color="#90b0fa"
                              v-if="project.owners[2]"
                              :fullname="project.owners[2].name"
                              :size="20"
                              style="margin-left: .3rem"
                          ></avatar>
                          <avatar
                              data-toggle="tooltip" data-placement="top" :title="project.owners[3].name"
                              color="#90b0fa"
                              v-if="project.owners[3]"
                              :fullname="project.owners[3].name"
                              :size="20"
                              style="margin-left: .3rem"
                          ></avatar>
                          <v-btn  icon @click="showAssignedOwners(project.id)" style="width: 1.5rem;height: 1.5rem;">
                              <i style="font-size: .6rem" class="fa fa-ellipsis-v"></i>
                          </v-btn>
                          <div v-if="showOwners && projectID ==project.id"  class="toggle-menu-card">
                              <div class="content" style="padding: 0 .4rem">
                                  <ul style="list-style: none;text-align: center; margin-top: 0.5rem;" class="toggle-div" v-for="(owner,index) in project.owners" :key="index">
                                      <li style="display: inline-flex; width: max-content;">
                                          <avatar color="#90b0fa" :fullname="owner.name" :size="30"></avatar>
                                          <router-link :to="'/admin/profile/' + owner.id" style="margin-left: 0.5rem">
                                              {{owner.name| subStr}}
                                          </router-link>
                                      </li>
                                  </ul>
                              </div>
                          </div>
                      </v-col>
                      <v-col cols="3" v-if="project.project_assign" style="display: inline-flex;">
                          <span style="font-weight: bold;font-size: .9rem">{{$t("Project.employees")}}:</span>
                          <avatar
                              data-placement="top" :title="project.project_assign[0].name"
                              color="#90b0fa"
                              v-if="project.project_assign[0]"
                              :fullname="project.project_assign[0].name"
                              :size="20"
                              style="margin-left: .3rem"
                          ></avatar>
                          <avatar
                              v-if="project.project_assign[1]"
                              data-placement="top" :title="project.project_assign[1].name"
                              color="#90b0fa"
                              :fullname="project.project_assign[1].name"
                              :size="20"
                              style="margin-left: .3rem"
                          ></avatar>
                          <avatar
                              v-if="project.project_assign[2]"
                              data-placement="top" :title="project.project_assign[2].name"
                              color="#90b0fa"
                              :fullname="project.project_assign[2].name"
                              :size="20"
                              style="margin-left: .3rem"
                          ></avatar>
                          <avatar
                              v-if="project.project_assign[3]"
                              data-placement="top" :title="project.project_assign[3].name"
                              color="#90b0fa"
                              :fullname="project.project_assign[3].name"
                              :size="20"
                              style="margin-left: .3rem"
                          ></avatar>
                          <v-btn icon @click="showAssigned(project.id)" style="width: 1.5rem;height: 1.5rem;">
                              <i style="font-size: .6rem" class="fa fa-ellipsis-v"></i>
                          </v-btn>
                          <div v-if="showAssignedUsers && projectID ==project.id"  class="toggle-menu-card">
                              <div class="content" style="padding: 0 .4rem">
                                  <ul style="list-style: none;text-align: center; margin-top: 0.5rem;" class="toggle-div" v-for="user in project.project_assign" :key="user.id">
                                      <li style="display: inline-flex; width: max-content;">
                                          <avatar color="#90b0fa" :fullname="user.name" :size="30"></avatar>
                                          <router-link :to="'/admin/profile/' + user.id" style="margin-left: 0.5rem">
                                              {{user.name| subStr}}
                                          </router-link>
                                      </li>
                                  </ul>
                              </div>
                          </div>
                      </v-col>
                  </v-row>
              </v-container>
          </v-card>
          <div class="col-md-12" style="border-radius: 15px">
              <v-tabs
                  v-model="ticketstab"
                  style="border-radius: 15px"
              >
                  <v-tabs-slider></v-tabs-slider>
                  <v-tab href="#tasks">
                      {{$t('Project.projectTasks')}}
                  </v-tab>
                  <v-tab href="#tickets">
                      {{$t('Project.projectTickets')}}
                  </v-tab>

              </v-tabs>

              <v-tabs-items v-model="ticketstab"  style="border-radius: 15px">
                  <v-tab-item value="tasks" style="border-radius: 15px">
                      <ProjectTasksList  v-if="showTasks" :tasks="tasks" :singlePage="true"></ProjectTasksList>
                  </v-tab-item>
                  <v-tab-item value="tickets" style="border-radius: 15px">
                      <ProjectTicketList v-if="showTickets" :tickets="tickets" :projectId="projectId" :singlePage="true"></ProjectTicketList>
                  </v-tab-item>
              </v-tabs-items>

              <project-comments></project-comments>

          </div>
      </v-container>



    <pagination
      align="center"
      size="small"
      :show-disabled="true"
      :data="tickets"
      :limit="3"
      @pagination-change-page="getTicketsByProjectId"
    ></pagination>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import moment from "moment";
import ProjectTicketList from '../../components/tickets/ProjectTicketList'
import ProjectTasksList from '../../components/tasks/ProjectTasksList'
import ProjectComments from '../../components/projects/ProjectComments'


export default {
  data() {
    return {
      showOwners: false,
      showAssignedUsers: false,
      projectId: '',
      favorite: false,
      showTickets: false,
      showTasks: false,
      ticketstab: 'ticketstable',


    };
  },
  methods: {
    addToFav(project_id){
      this.$Progress.start();
      this.$store
        .dispatch("project/addFavorites", {project_id: project_id, single: true})

        .then(response => {
            this.$Progress.finish();
            this.favorite = true;
            Toast.fire({
                type: "success",
                title: 'project added to favorites!'
            });
            this.getPrjectById(this.projectId)
        })
        .catch(error => {
          console.log(error)

            this.$Progress.fail();
        });
    },
    removeFav(project_id){
      this.$Progress.start();
      this.$store
        .dispatch("project/removeFavorites", {project_id: project_id, single: true})
        .then(response => {
            this.$Progress.finish();
            this.favorite = false;
            Toast.fire({
                type: "success",
                title: 'project removed from favorites!'
            });
            this.getPrjectById(this.projectId)

        })
        .catch(error => {
            this.$Progress.fail();
        });
    },
    getPrjectById(id) {
      this.$Progress.start();
      this.$store
        .dispatch("project/getProjectById", id)
        .then(response => {
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    getTicketsByProjectId(page = 1) {
      this.$Progress.start();
      this.$store
        .dispatch("ticket/getTicketsByProjectId", {
          id: this.projectId,
          page: page,
          index:true,
          dropdown:true
        })
        .then(response => {
          this.showTickets = true;
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    deleteProject(id) {
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
            .dispatch("project/deleteProject", id)
            .then(response => {
              this.$Progress.finish();
              Toast.fire({
                type: "success",
                title: response.data.message
              });
              this.$router.push({ name: "projects.list" });
            })
            .catch(error => {
              this.$Progress.fail();
              Toast.fire({
                type: "error",
                title: error.response.data.message
              });
            });
        }
      });
    },
    getFavoriets() {
      this.$store
        .dispatch("project/getFavorites")
        .then(response => {
          response.data.data.forEach(item => {
            if(item.id == this.projectId) {
              this.favorite = true;
            }
          })
        })
        .catch(error => {
            console.log(error);
        });
    },
    getUserById(id) {
      this.$Progress.start();
      this.$store
        .dispatch("user/getUserById", id)
        .then(response => {
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    rolesFilters (items) {
      items.forEach(item => {
        item.name == 'admin' ? true : false;
      })
    },
    getTasksByProjectId(page = 1) {
      this.$Progress.start();
      this.$store
        .dispatch("task/getTasksByProjectId", { id: this.projectId, page: page,index:true,dropdown:true
 })
        .then((response) => {
          this.showTasks = true;
          this.$Progress.finish();
        })
        .catch((error) => {
          this.$Progress.fail();
        });
    },
      showAssigned(id) {
          this.projectID = id;
          this.showAssignedUsers = !this.showAssignedUsers;
          this.showOwners = false;
      },
      showAssignedOwners(id) {
          this.projectID = id;
          this.showOwners = !this.showOwners;
          this.showAssignedUsers = false;
      },
  },
  created() {
    this.projectId = parseInt(this.$route.params.id);
    this.getPrjectById(this.projectId);
    this.getTicketsByProjectId();
    this.getTasksByProjectId();
    this.rolesFilters(this.user.roles);
    this.getFavoriets();
    this.getUserById(this.$userId);
  },
  mounted() {


  },
  computed: {
    ...mapGetters({
      project: "project/activeSingleProject",
      tickets: "ticket/activeTickets",
      tasks: "task/activeTasks",
      user: "user/activeSingleUser",
      favorites: "project/getFavorites"
    }),
  },
  filters:{
    subStr: (value) => {
        if(value.length > 9) {
            return value.substring(0, 12) + '...';
        }
        return value;
    },
      subProject: (value) => {
          if(value.length > 15) {
              return value.substring(0, 15) + '...';
          }
          return value;
      },

    projectsDateNow: (value) => {
        return moment(value).fromNow()
    },
  },
  components: {
    ProjectTicketList,
    ProjectTasksList,
    ProjectComments
  }
};
</script>

<style scoped>
.actions-btn-cont{
    height: 70%;
    border-radius: 15px;
    border:1px solid #b8b8b8;
    background-color: #ffffff;
    padding:0 1.2rem;
    margin-top:12%;
    transform: translateY(12%);
}

.actions-btn-cont .action{
    height: 50%;
    width: 100%;
}

.action .actionIcon i{
    margin-top: 17%;
    margin-left: 1.7rem;
    font-size: 1.3rem;

    transform: translateY(50%) translateX(-50%);
}

/*colors*/
#open{
    background-color: #A0466F;
}
#done{
    background-color: #67A046;
}
#in-progress{
    background-color: #3490dc;
}
#pending{
    background-color: #EEA24C;
}
/*end colors*/

.stat{
    font-weight: bold;
    padding: 0.7rem 0.7rem 0px 0px;
    text-align: left;
    color: #ffffff;
    height:2.8rem;
}


/*new style*/
.status_container{
    width: 20rem;
    height: 1.9rem;
    border-bottom-right-radius: 50%;
    border-top-left-radius: 50%;
    text-align: center;
}

.stat-name {
    font-weight: bold;
    font-size: .9rem;
    color: #ffffff;
    transform: translateY(2%);
    text-transform: capitalize;
}
.actions-container{
    width: 1.8rem;
    height: 1.8rem;
    border-radius: 50%;
    background: linear-gradient(180deg, #DDB456 29.5%, #234fa3 100%);
    text-align: center;
    margin-left: 1rem;

}
.fas,.far{
    color: #ffffff;
}


/*new style*/

</style>
