<template>
  <div>
      <v-col cols="12">
          <v-row style="margin-top:1rem">
              <v-col cols="3">
                  <v-text-field
                      v-model="queryParams.global_search"
                      append-icon="fas fa-search"
                      hide-details
                      solo
                      clear-icon="fas fa-clear"
                      clearable
                      label="Global Search"
                      type="text"
                      style="border-radius: 10px;"
                      @change="getResults"
                  ></v-text-field>
              </v-col>
              <v-col cols="3">
                  <v-text-field
                      @change="multiSearch('name')"
                      v-model="search.name"
                      append-icon="fas fa-search"
                      hide-details
                      solo
                      clear-icon="fas fa-clear"
                      clearable
                      label="UserName"
                      type="text"
                      style="border-radius: 10px;"
                  ></v-text-field>
              </v-col>
              <v-col cols="2">
                  <v-text-field
                      @change="multiSearch('email')"
                      v-model="search.email"
                      append-icon="fas fa-search"
                      hide-details
                      solo
                      clear-icon="fas fa-clear"
                      clearable
                      label="Email"
                      type="text"
                      style="border-radius: 10px;"
                  ></v-text-field>
              </v-col>
              <v-col cols="2">
                  <v-text-field
                      @change="multiSearch('role')"
                      v-model="search.role"
                      append-icon="fas fa-search"
                      hide-details
                      solo
                      clear-icon="fas fa-clear"
                      clearable
                      label="Role"
                      type="text"
                      style="border-radius: 10px;"
                  ></v-text-field>
              </v-col>
              <v-col cols="2">
                  <v-btn bottom style=" font-size: 1rem;background-color: #2F5298;color: #ffffff;height: 3rem" block @click="getResults">Search</v-btn>
              </v-col>
              <v-col cols="1">
                  <v-btn color="#2F5298" dark fab small bottom right fixed @click="newModal()">
                      <v-icon>fas fa-plus</v-icon>
                  </v-btn>
              </v-col>
          </v-row>
          <v-row style="margin-top:.5rem">
              <v-col cols="3">
                  <v-text-field
                      @change="multiSearch('created_at')"
                      v-model="search.created_at"
                      append-icon="fas fa-search"
                      hide-details
                      solo
                      clear-icon="fas fa-clear"
                      clearable
                      label="CreatedAt"
                      type="date"
                      style="border-radius: 10px;"
                  ></v-text-field>
              </v-col>
              <v-col cols="2">
                  <select
                      class="form-control"
                      @change="multiSearch('lang')"
                      v-model="search.lang"
                  >
                      <option value="all" selected="selected">All</option>
                      <option
                          v-for="(lang, index) in langs"
                          :value="lang.value"
                          :key="index"
                      >
                          {{ lang.name }}
                      </option>
                  </select>
              </v-col>
              <v-col cols="5">
                  <div class="filter">
                      <div >
                          <v-row >
                              <v-col cols="5" class="p-0">
                                  <select
                                      class="form-control p-0"
                                      @change="resetFilter"
                                      v-model="attribute_filter"
                                      title="Attribute Filter"
                                      name="sad"
                                  >
                                      <option>All</option>
                                      <option
                                          v-for="(attribute, index) in attributes"
                                          :value="attribute"
                                          :key="index"
                                      >
                                          {{ attribute.name }}
                                      </option>
                                  </select>
                              </v-col>
                              <v-col cols="4" v-if="attribute_filter">
                                  <div class="row" v-if="attribute_filter!='All'">
                                      <div
                                          v-if="attribute_filter && attribute_filter.type == 'dropdown'"
                                          class="col-md-12 p-0"
                                      >
                                          <div class="form-group">
                                              <select
                                                  class="form-control p-0 placeholder-select"
                                                  v-model="attribute_filter_data"
                                              >
                                                  <option
                                                      v-for="(value, index) in attribute_filter.values"
                                                      :key="index"
                                                      :value="value"
                                                  >
                                                      {{ value }}
                                                  </option>
                                              </select>
                                          </div>
                                      </div>

                                      <div v-if="attribute_filter && attribute_filter.type == 'text'" class="col-md-12 p-0" >
                                          <div class="form-group">
                                              <input
                                                  type="text"
                                                  class="form-control p-0"
                                                  v-model="attribute_filter_data"
                                              />
                                          </div>
                                      </div>
                                      <div
                                          v-if="attribute_filter && attribute_filter.type == 'checkbox'"
                                          class="col-md-12 p-0"
                                      >
                                          <div
                                              class="form-check"
                                              v-for="(value, cindex) in attribute_filter.values"
                                              :key="cindex"
                                          >
                                              <input
                                                  class="form-check-input"
                                                  type="checkbox"
                                                  :value="value"
                                                  :name="'checkbox[' + value + ']'"
                                                  v-model="attribute_filter_data"
                                              />
                                              <label class="form-check-label" for="defaultCheck1">{{
                                                      value
                                                  }}</label>
                                          </div>
                                      </div>
                                      <div
                                          v-if="
                            attribute_filter && attribute_filter.type == 'radiobutton'"
                                          class="col-md-12 p-0"
                                      >
                                          <div
                                              class="form-check"
                                              v-for="(value, cindex) in attribute_filter.values"
                                              :key="cindex"
                                          >
                                              <label class="form-check-label">
                                                  <input
                                                      type="radio"
                                                      class="form-check-input"
                                                      :value="value"
                                                      v-model="attribute_filter_data"
                                                      name="value"
                                                  />
                                                  {{ value }}
                                              </label>
                                          </div>
                                      </div>
                                  </div>
                              </v-col>
                              <v-col cols="4" v-else>
                                  <input disabled  value="Select Attribute">
                              </v-col>
                              <v-col cols="3" class="p-0">
                                  <v-btn
                                      dark
                                      color="#2F5298"
                                      style="height: 3rem"
                                      @click="filter_users_by_attribute"
                                  >
                                      <v-icon dark>fas  fa-filter</v-icon>
                                  </v-btn>
                              </v-col>
                          </v-row>
                      </div>
                  </div>
              </v-col>


              <v-col cols="2">
                  <v-btn bottom style=" font-size: 1rem;background-color: #234fa3;color: #ffffff;height: 3rem" block @click="clearQuery">Reset Query</v-btn>
              </v-col>
          </v-row>
      </v-col>


    <v-row>
      <v-col cols="12">
        <v-simple-table dense fixed-header style="margin-top:.5rem">
          <template v-slot:default>
            <thead>
              <tr>
                <th
                  width="1%"                  
                  class="text-left text-bold"
                  style="color: #234fa3">
                  Number
                </th>
                <th
                  width="15%"
                  class="text-left text-bold"
                  style="color: #234fa3"
                >
                  UserName
                </th>
                <th
                  width="20%"
                  class="text-left text-bold"
                  style="color: #234fa3"
                >
                  Email
                </th>
                <th
                  width="15%"
                  class="text-left text-bold"
                  style="color: #234fa3"
                >
                  Type
                </th>
                <th
                  width="20%"
                  class="text-left text-bold"
                  style="color: #234fa3"
                >
                  Roles
                </th>
                <th
                  width="15%"
                  class="text-left text-bold"
                  style="color: #234fa3"
                >
                  Created At
                </th>
                <th
                  width="15%"
                  class="text-left text-bold"
                  style="color: #234fa3"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users.data" :key="user.id">
                <td> {{user.id}} </td>
                <td>
                  <router-link :to="'/admin/profile/' + user.id">{{
                    user.name
                  }}</router-link>
                </td>
                <td>{{ user.email }}</td>
                <td>{{ user.type == "regular-user"? "Employee" : "Client"  }}</td>
                <td>
                  <div
                    v-show="user.roles"
                    v-for="role in user.roles"
                    :key="role.id"
                    class="badge badge-primary mr-1"
                  >
                    {{ role.name }}
                  </div>
                </td>
                <td>{{ user.created_at | DateWithTime }}</td>
                <td>
                  <i
                    data-widget="collapse"
                    data-toggle="tooltip"
                    title="Edit"
                    class="text-success icon fas fa-edit fa-s"
                    style="
                      :hover {
                        color: #ffffff;
                      }
                    "
                    @click="editModal(user)"
                  >
                  </i>
                  <i
                    data-widget="collapse"
                    data-toggle="tooltip"
                    title="Edit"
                    class="text-danger icon pl-1 fas fa-trash fa-xs"
                    @click="deleteUser(user.id)"
                  >
                  </i>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
        <pagination
          :data="users"
          :limit="parseInt(2)"
          size="small"
          @pagination-change-page="changePage"
        >
          <span slot="prev-nav">&lt; Previous</span>
          <span slot="next-nav">Next &gt;</span>
        </pagination>
      </v-col>
    </v-row>
    <!-- Modal -->
    <div
      class="modal fade rounded"
      id="Modal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="newUserLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #DCDCDC">
            <h4 class="modal-title bold" id="newUserLabel" style="font-weight: bold">
              {{ $t("User.userDetails") }}
            </h4>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <ul class="nav nav-pills ml-auto p-2">
              <li class="nav-item tapStyle">
                <a class="nav-link active" href="#tab_1" data-toggle="tab">{{
                  $t("User.accountSetting")
                }}</a>
              </li>
              <li class="nav-item tapStyle">
                <a class="nav-link" href="#tab_2" data-toggle="tab">{{
                  $t("User.accountDetails")
                }}</a>
              </li>

              <li class="nav-item tapStyle">
                <a class="nav-link" href="#tab_3" data-toggle="tab"
                  >Attributes</a
                >
              </li>
            </ul>
            <v-card color="#DCDCDC">
                <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                  <form
                    @submit.prevent="editMode ? editUser(form.id) : saveForms()"
                    @keydown="form.onKeydown($event)"
                  >
                    <div class="modal-body">

                      <v-row>
                        <v-col>
                          <div class="form-group">
                            <label for="name">{{ $t("User.username") }}</label>
                            <input
                              v-model="form.name"
                              type="text"
                              name="name"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors.has('name') }"
                            />
                            <has-error :form="form" field="name"></has-error>
                          </div>
                        </v-col>
                        <v-col>
                          <div class="form-group">
                            <label for="email">{{ $t("User.email") }}</label>
                            <input
                              v-model="form.email"
                              type="email"
                              name="email"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors.has('email') }"
                            />
                            <has-error :form="form" field="email"></has-error>
                          </div>
                        </v-col>
                      </v-row>
                      <v-row>
                        <v-col>
                          <div class="form-group">
                            <label for="password">{{ $t("User.password") }}</label>
                            <input
                              v-model="form.password"
                              type="password"
                              name="password"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors.has('password') }"
                            />
                            <has-error :form="form" field="password"></has-error>
                          </div>
                        </v-col>
                        <v-col>
                          <div class="form-group">
                            <label for="role">{{ $t("User.role") }}</label>
                            <multiselect
                              v-model="form.roles"
                              :multiple="true"
                              :options="roles"
                              :close-on-select="true"
                              :placeholder="$t('User.selectOne')"
                              label="name"
                              track-by="name"
                            ></multiselect>
                            <has-error :form="form" field="role"></has-error>
                          </div>
                        </v-col>
                      </v-row>
                      <!-- <v-row>
                        <v-col>
                          <div class="form-group">
                            <label for="type">{{ $t("User.type") }}</label>
                            <multiselect
                              v-model="form.type"
                              :options="types"
                              track-by="value"
                              label="label"
                              :searchable="false"
                              :close-on-select="true"
                              :show-labels="false"
                              :placeholder="$t('User.pickValue')"
                            ></multiselect>
                            <has-error :form="form" field="type"></has-error>
                          </div>
                        </v-col>
                      </v-row> -->
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary" style="background-color:#234FA3; color:white;">
                        {{ $t("User.save") }}
                      </button>
                    </div>
                  </form>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_2">
                  <form
                    @submit.prevent="
                      editMode ? editMetadata(metadata) : saveForms()
                    "
                    @keydown="form.onKeydown($event)"
                  >
                    <div class="modal-body">
                      <v-row>
                        <v-col>
                          <div class="form-group">
                            <label for="first_name">{{ $t("User.firstName") }}</label>
                            <input
                              v-model="metadata.first_name"
                              type="text"
                              name="first_name"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors.has('first_name') }"
                              autocomplete="off"
                            />
                            <has-error :form="form" field="first_name"></has-error>
                          </div>
                        </v-col>
                        <v-col>
                          <div class="form-group">
                            <label for="last_name">{{ $t("User.lastName") }}</label>
                            <input
                              v-model="metadata.last_name"
                              type="text"
                              name="last_name"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors.has('last_name') }"
                              autocomplete="off"
                            />
                            <has-error :form="form" field="last_name"></has-error>
                          </div>
                        </v-col>
                      </v-row>
                      <v-row>
                        <v-col>
                          <div class="form-group">
                            <label for="company">{{ $t("User.companyName") }}</label>
                            <input
                              v-model="metadata.company"
                              type="text"
                              name="company"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors.has('company') }"
                              autocomplete="off"
                            />
                            <has-error :form="form" field="company"></has-error>
                          </div>
                        </v-col>
                        <v-col>
                          <div class="form-group">
                            <label for="position">{{
                              $t("User.positionName")
                            }}</label>
                            <input
                              v-model="metadata.position"
                              type="text"
                              name="position"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors.has('position') }"
                              autocomplete="off"
                            />
                            <has-error :form="form" field="position"></has-error>
                          </div>
                        </v-col>
                      </v-row>
                      <v-row>
                        <v-col>
                          <div class="form-group">
                            <label for="address">{{ $t("User.address") }}</label>
                            <input
                              v-model="metadata.address"
                              type="text"
                              name="address"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors.has('address') }"
                              autocomplete="off"
                            />
                            <has-error :form="form" field="address"></has-error>
                          </div>
                        </v-col>
                        <v-col>
                          <div class="form-group">
                            <label for="language">{{ $t("User.lang") }}</label>
                            <multiselect
                              v-model="metadata.language"
                              :options="language"
                              :searchable="false"
                              :close-on-select="true"
                              :show-labels="false"
                              :placeholder="$t('User.pickValue')"
                            ></multiselect>
                            <has-error :form="form" field="language"></has-error>
                          </div>
                        </v-col>
                      </v-row>
                      <v-row>
                        <v-col>
                          <div class="form-group">
                            <label for="gender">{{ $t("User.gender") }}</label>
                            <multiselect
                              v-model="metadata.gender"
                              :options="gender"
                              :searchable="false"
                              :close-on-select="true"
                              :show-labels="false"
                              :placeholder="$t('User.pickValue')"
                            ></multiselect>
                            <has-error :form="form" field="gender"></has-error>
                          </div>
                        </v-col>
                        <v-col>
                          <div class="form-group">
                            <label for="telephone">{{ $t("User.tele") }}</label>
                            <input
                              v-model="metadata.telephone"
                              type="text"
                              name="telephone"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors.has('telephone') }"
                              autocomplete="off"
                            />
                            <has-error :form="form" field="telephone"></has-error>
                          </div>
                        </v-col>
                      </v-row>
                      <v-row>
                        <v-col>
                          <div class="form-group">
                            <label for="mobile">{{ $t("User.mobile") }}</label>
                            <input
                              v-model="metadata.mobile"
                              type="text"
                              name="mobile"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors.has('mobile') }"
                              autocomplete="off"
                            />
                            <has-error :form="form" field="mobile"></has-error>
                          </div>
                        </v-col>
                        <v-col>
                          <div class="form-group">
                            <label for="fax">{{ $t("User.fax") }}</label>
                            <input
                              v-model="metadata.fax"
                              type="text"
                              name="fax"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors.has('fax') }"
                              autocomplete="off"
                            />
                            <has-error :form="form" field="fax"></has-error>
                          </div>
                        </v-col>
                      </v-row>
                      <v-row>
                        <v-col>
                          <div class="form-group">
                            <label for="website">{{ $t("User.website") }}</label>
                            <input
                              v-model="metadata.website"
                              type="text"
                              name="website"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors.has('website') }"
                              autocomplete="off"
                            />
                            <has-error :form="form" field="website"></has-error>
                          </div>
                        </v-col>
                        <v-col>
                          <div class="form-group">
                            <label for="birth_date">{{ $t("User.birthDate") }}</label>
                            <date-picker
                              v-model="metadata.birth_date"
                              lang="en"
                              type="date"
                              format="DD-MM-YYYY"
                              :minute-step="1"
                              value-type="format"
                              input-class="form-control"
                            ></date-picker>
                            <has-error :form="form" field="birth_date"></has-error>
                          </div>
                        </v-col>
                      </v-row>

                      <v-row>
                        <v-col>
                          <div class="form-group">
                            <label for="website">{{ $t("User.timezone") }}</label>
                            <multiselect
                              v-model="metadata.timezone"
                              :options="timezones"
                              :searchable="true"
                              :close-on-select="true"
                              :show-labels="false"
                              :placeholder="$t('User.pickValue')"
                            ></multiselect>
                            <has-error :form="form" field="timezone"></has-error>
                          </div>
                        </v-col>
                      </v-row>


                      <v-row>
                        <v-col>
                          <div class="form-group">
                            <label for="fax">{{ $t("User.signature") }}</label>
                            <quill-editor
                              id="comments-editor"
                              v-model="metadata.signature"
                              ref="myQuillEditor"
                              :options="editorOption"
                              style="background-color:#ffffff"
                            ></quill-editor>
                            <has-error :form="form" field="fax"></has-error>
                          </div>
                        </v-col>
                      </v-row>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary" style="background-color:#234FA3; color:white;">
                        {{ $t("User.save") }}
                      </button>
                    </div>
                  </form>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_3">
                  <ul class="list-group">
                    <li
                      class="list-group-item"
                      v-for="(user_attribute, pindex) in user_selected_attributes"
                      :key="pindex"
                      :id="'wrapper-' + pindex"
                    >
                      <div class="form-group row">
                        <div class="col-md-6">
                          <select
                            @change="change_attribute(pindex)"
                            class="form-control"
                            v-model="attribute_selector[pindex]"
                          >
                            <option></option>
                            <option
                              v-for="(attribute, aindex) in attributes"
                              :key="aindex"
                              :value="attribute"
                            >
                              {{ attribute.name }}
                            </option>
                          </select>
                        </div>
                        <div
                          v-if="
                            attribute_selector[pindex] &&
                            attribute_selector[pindex].type == 'dropdown'
                          "
                          class="col-md-6"
                        >
                          <div class="form-group row">
                            <select
                              class="form-control"
                              v-model="user_selected_attributes[pindex].dropdown"
                            >
                              <option
                                v-for="(value, index) in attribute_selector[
                                  pindex
                                ].values"
                                :key="index"
                                :value="value"
                              >
                                {{ value }}
                              </option>
                            </select>
                          </div>
                        </div>

                        <div
                          v-if="
                            attribute_selector[pindex] &&
                            attribute_selector[pindex].type == 'text'
                          "
                          class="col-md-6"
                        >
                          <div class="form-group row">
                            <input
                              type="text"
                              class="form-control"
                              v-model="user_selected_attributes[pindex].text"
                            />
                          </div>
                        </div>
                        <div
                          v-if="
                            attribute_selector[pindex] &&
                            attribute_selector[pindex].type == 'checkbox'
                          "
                          class="col-md-6"
                        >
                          <div
                            class="form-check"
                            v-for="(value, index) in attribute_selector[pindex]
                              .values"
                            :key="index"
                          >
                            <input
                              class="form-check-input"
                              type="checkbox"
                              :value="value"
                              :name="'value[' + pindex + '][' + index + ']'"
                              v-model="user_selected_attributes[pindex].checkbox"
                            />
                            <label class="form-check-label" for="defaultCheck1">{{
                              value
                            }}</label>
                          </div>
                        </div>

                        <div
                          v-if="
                            attribute_selector[pindex] &&
                            attribute_selector[pindex].type == 'radiobutton'
                          "
                        >
                          <div
                            class="form-check"
                            v-for="(value, cindex) in attribute_selector[pindex]
                              .values"
                            :key="cindex"
                          >
                            <label class="form-check-label">
                              <input
                                type="radio"
                                class="form-check-input"
                                :value="value"
                                v-model="
                                  user_selected_attributes[pindex].radiobutton
                                "
                                :name="'value[' + pindex + ']'"
                              />
                              {{ value }}
                            </label>
                          </div>
                        </div>
                      </div>
                      <!-- form group -->

                      <div
                        class="user_attributes float-right"
                        @click="delete_user_attributes(pindex)"
                      >
                        <a href="javascript:;">
                          <i class="fas fa-trash-alt"></i>
                        </a>
                      </div>
                    </li>
                  </ul>

                  <div
                    class="user_attributes float-right"
                    @click="increase_user_attributes"
                  >
                    <a href="javascript:;" class="btn btn-success">
                      <i class="fas fa-plus-square"></i>
                    </a>
                  </div>

                  <div
                    class="save_attributes float-right"
                    @click="save_attributes"
                  >
                    <a href="javascript:;" class="btn btn-primary">
                      <i class="fas fa-save"></i>
                    </a>
                  </div>

                  <span>
                    <router-link :to="'/admin/attributes/' + type"
                      >New Attribute</router-link
                    >
                  </span>
                </div>
                <!-- /.tab-pane -->
              </div>
            </v-card>
            <!-- /.tab-content -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import userApi from "../../api/users";
import roleApi from "../../api/roles";
import { mapGetters } from "vuex";
import DatePicker from "vue2-datepicker";

import { quillEditor } from "vue-quill-editor";

// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
  data() {
    return {
      search: {
        name: "",
        email: "",
        role: "",
        lang: "",
        created_at: "",
      },
      queryParams: {
        filters: [],
        global_search: "",
        per_page: 15,
        page: 1,
      },
      global_search: "",
      type: "employee",
      editMode: false,
      user_attributes: [{}],
      attribute_selector: [],
      attribute_filter: null,
      attribute_filter_data: [],
      excel_export: false,
      user_selected_attributes: [
        {
          attribute_id: "",
          checkbox: [],
          radiobutton: [],
          dropdown: [],
          text: [],
        },
      ],
      // users: {},
      form: new Form({
        id: "",
        name: "",
        email: "",
        password: "",
        roles: "",
        attributes: "",
        type: "regular-user",
      }),
      metadata: new Form({
        user_id: "",
        id: "",
        first_name: "",
        last_name: "",
        company: "",
        address: "",
        language: "",
        gender: "",
        telephone: "",
        mobile: "",
        fax: "",
        website: "",
        birth_date: "",
        signature: "",
        attributes: "",
        timezone: ""
      }),
      editorOption: {
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [
              {
                list: "ordered",
              },
              {
                list: "bullet",
              },
            ],
            ["link", "image"],
          ],
        },
      },
      language: ["de", "en"],
      timezones: [
        "Africa/Abidjan",
        "Africa/Accra",
        "Africa/Algiers",
        "Africa/Bissau",
        "Africa/Cairo",
        "Africa/Casablanca",
        "Africa/Ceuta",
        "Africa/El_Aaiun",
        "Africa/Johannesburg",
        "Africa/Juba",
        "Africa/Khartoum",
        "Africa/Lagos",
        "Africa/Maputo",
        "Africa/Monrovia",
        "Africa/Nairobi",
        "Africa/Ndjamena",
        "Africa/Sao_Tome",
        "Africa/Tripoli",
        "Africa/Tunis",
        "Africa/Windhoek",
        "America/Adak",
        "America/Anchorage",
        "America/Araguaina",
        "America/Argentina/Buenos_Aires",
        "America/Argentina/Catamarca",
        "America/Argentina/Cordoba",
        "America/Argentina/Jujuy",
        "America/Argentina/La_Rioja",
        "America/Argentina/Mendoza",
        "America/Argentina/Rio_Gallegos",
        "America/Argentina/Salta",
        "America/Argentina/San_Juan",
        "America/Argentina/San_Luis",
        "America/Argentina/Tucuman",
        "America/Argentina/Ushuaia",
        "America/Asuncion",
        "America/Atikokan",
        "America/Bahia",
        "America/Bahia_Banderas",
        "America/Barbados",
        "America/Belem",
        "America/Belize",
        "America/Blanc-Sablon",
        "America/Boa_Vista",
        "America/Bogota",
        "America/Boise",
        "America/Cambridge_Bay",
        "America/Campo_Grande",
        "America/Cancun",
        "America/Caracas",
        "America/Cayenne",
        "America/Chicago",
        "America/Chihuahua",
        "America/Costa_Rica",
        "America/Creston",
        "America/Cuiaba",
        "America/Curacao",
        "America/Danmarkshavn",
        "America/Dawson",
        "America/Dawson_Creek",
        "America/Denver",
        "America/Detroit",
        "America/Edmonton",
        "America/Eirunepe",
        "America/El_Salvador",
        "America/Fort_Nelson",
        "America/Fortaleza",
        "America/Glace_Bay",
        "America/Goose_Bay",
        "America/Grand_Turk",
        "America/Guatemala",
        "America/Guayaquil",
        "America/Guyana",
        "America/Halifax",
        "America/Havana",
        "America/Hermosillo",
        "America/Indiana/Indianapolis",
        "America/Indiana/Knox",
        "America/Indiana/Marengo",
        "America/Indiana/Petersburg",
        "America/Indiana/Tell_City",
        "America/Indiana/Vevay",
        "America/Indiana/Vincennes",
        "America/Indiana/Winamac",
        "America/Inuvik",
        "America/Iqaluit",
        "America/Jamaica",
        "America/Juneau",
        "America/Kentucky/Louisville",
        "America/Kentucky/Monticello",
        "America/La_Paz",
        "America/Lima",
        "America/Los_Angeles",
        "America/Maceio",
        "America/Managua",
        "America/Manaus",
        "America/Martinique",
        "America/Matamoros",
        "America/Mazatlan",
        "America/Menominee",
        "America/Merida",
        "America/Metlakatla",
        "America/Mexico_City",
        "America/Miquelon",
        "America/Moncton",
        "America/Monterrey",
        "America/Montevideo",
        "America/Nassau",
        "America/New_York",
        "America/Nipigon",
        "America/Nome",
        "America/Noronha",
        "America/North_Dakota/Beulah",
        "America/North_Dakota/Center",
        "America/North_Dakota/New_Salem",
        "America/Nuuk",
        "America/Ojinaga",
        "America/Panama",
        "America/Pangnirtung",
        "America/Paramaribo",
        "America/Phoenix",
        "America/Port-au-Prince",
        "America/Port_of_Spain",
        "America/Porto_Velho",
        "America/Puerto_Rico",
        "America/Punta_Arenas",
        "America/Rainy_River",
        "America/Rankin_Inlet",
        "America/Recife",
        "America/Regina",
        "America/Resolute",
        "America/Rio_Branco",
        "America/Santarem",
        "America/Santiago",
        "America/Santo_Domingo",
        "America/Sao_Paulo",
        "America/Scoresbysund",
        "America/Sitka",
        "America/St_Johns",
        "America/Swift_Current",
        "America/Tegucigalpa",
        "America/Thule",
        "America/Thunder_Bay",
        "America/Tijuana",
        "America/Toronto",
        "America/Vancouver",
        "America/Whitehorse",
        "America/Winnipeg",
        "America/Yakutat",
        "America/Yellowknife",
        "Antarctica/Casey",
        "Antarctica/Davis",
        "Antarctica/DumontDUrville",
        "Antarctica/Macquarie",
        "Antarctica/Mawson",
        "Antarctica/Palmer",
        "Antarctica/Rothera",
        "Antarctica/Syowa",
        "Antarctica/Troll",
        "Antarctica/Vostok",
        "Asia/Almaty",
        "Asia/Amman",
        "Asia/Anadyr",
        "Asia/Aqtau",
        "Asia/Aqtobe",
        "Asia/Ashgabat",
        "Asia/Atyrau",
        "Asia/Baghdad",
        "Asia/Baku",
        "Asia/Bangkok",
        "Asia/Barnaul",
        "Asia/Beirut",
        "Asia/Bishkek",
        "Asia/Brunei",
        "Asia/Chita",
        "Asia/Choibalsan",
        "Asia/Colombo",
        "Asia/Damascus",
        "Asia/Dhaka",
        "Asia/Dili",
        "Asia/Dubai",
        "Asia/Dushanbe",
        "Asia/Famagusta",
        "Asia/Gaza",
        "Asia/Hebron",
        "Asia/Ho_Chi_Minh",
        "Asia/Hong_Kong",
        "Asia/Hovd",
        "Asia/Irkutsk",
        "Asia/Jakarta",
        "Asia/Jayapura",
        "Asia/Jerusalem",
        "Asia/Kabul",
        "Asia/Kamchatka",
        "Asia/Karachi",
        "Asia/Kathmandu",
        "Asia/Khandyga",
        "Asia/Kolkata",
        "Asia/Krasnoyarsk",
        "Asia/Kuala_Lumpur",
        "Asia/Kuching",
        "Asia/Macau",
        "Asia/Magadan",
        "Asia/Makassar",
        "Asia/Manila",
        "Asia/Nicosia",
        "Asia/Novokuznetsk",
        "Asia/Novosibirsk",
        "Asia/Omsk",
        "Asia/Oral",
        "Asia/Pontianak",
        "Asia/Pyongyang",
        "Asia/Qatar",
        "Asia/Qostanay",
        "Asia/Qyzylorda",
        "Asia/Riyadh",
        "Asia/Sakhalin",
        "Asia/Samarkand",
        "Asia/Seoul",
        "Asia/Shanghai",
        "Asia/Singapore",
        "Asia/Srednekolymsk",
        "Asia/Taipei",
        "Asia/Tashkent",
        "Asia/Tbilisi",
        "Asia/Tehran",
        "Asia/Thimphu",
        "Asia/Tokyo",
        "Asia/Tomsk",
        "Asia/Ulaanbaatar",
        "Asia/Urumqi",
        "Asia/Ust-Nera",
        "Asia/Vladivostok",
        "Asia/Yakutsk",
        "Asia/Yangon",
        "Asia/Yekaterinburg",
        "Asia/Yerevan",
        "Atlantic/Azores",
        "Atlantic/Bermuda",
        "Atlantic/Canary",
        "Atlantic/Cape_Verde",
        "Atlantic/Faroe",
        "Atlantic/Madeira",
        "Atlantic/Reykjavik",
        "Atlantic/South_Georgia",
        "Atlantic/Stanley",
        "Australia/Adelaide",
        "Australia/Brisbane",
        "Australia/Broken_Hill",
        "Australia/Darwin",
        "Australia/Eucla",
        "Australia/Hobart",
        "Australia/Lindeman",
        "Australia/Lord_Howe",
        "Australia/Melbourne",
        "Australia/Perth",
        "Australia/Sydney",
        "CET",
        "CST6CDT",
        "EET",
        "EST",
        "EST5EDT",
        "Etc/GMT",
        "Etc/GMT+1",
        "Etc/GMT+10",
        "Etc/GMT+11",
        "Etc/GMT+12",
        "Etc/GMT+2",
        "Etc/GMT+3",
        "Etc/GMT+4",
        "Etc/GMT+5",
        "Etc/GMT+6",
        "Etc/GMT+7",
        "Etc/GMT+8",
        "Etc/GMT+9",
        "Etc/GMT-1",
        "Etc/GMT-10",
        "Etc/GMT-11",
        "Etc/GMT-12",
        "Etc/GMT-13",
        "Etc/GMT-14",
        "Etc/GMT-2",
        "Etc/GMT-3",
        "Etc/GMT-4",
        "Etc/GMT-5",
        "Etc/GMT-6",
        "Etc/GMT-7",
        "Etc/GMT-8",
        "Etc/GMT-9",
        "Etc/UTC",
        "Europe/Amsterdam",
        "Europe/Andorra",
        "Europe/Astrakhan",
        "Europe/Athens",
        "Europe/Belgrade",
        "Europe/Berlin",
        "Europe/Brussels",
        "Europe/Bucharest",
        "Europe/Budapest",
        "Europe/Chisinau",
        "Europe/Copenhagen",
        "Europe/Dublin",
        "Europe/Gibraltar",
        "Europe/Helsinki",
        "Europe/Istanbul",
        "Europe/Kaliningrad",
        "Europe/Kiev",
        "Europe/Kirov",
        "Europe/Lisbon",
        "Europe/London",
        "Europe/Luxembourg",
        "Europe/Madrid",
        "Europe/Malta",
        "Europe/Minsk",
        "Europe/Monaco",
        "Europe/Moscow",
        "Europe/Oslo",
        "Europe/Paris",
        "Europe/Prague",
        "Europe/Riga",
        "Europe/Rome",
        "Europe/Samara",
        "Europe/Saratov",
        "Europe/Simferopol",
        "Europe/Sofia",
        "Europe/Stockholm",
        "Europe/Tallinn",
        "Europe/Tirane",
        "Europe/Ulyanovsk",
        "Europe/Uzhgorod",
        "Europe/Vienna",
        "Europe/Vilnius",
        "Europe/Volgograd",
        "Europe/Warsaw",
        "Europe/Zaporozhye",
        "Europe/Zurich",
        "HST",
        "Indian/Chagos",
        "Indian/Christmas",
        "Indian/Cocos",
        "Indian/Kerguelen",
        "Indian/Mahe",
        "Indian/Maldives",
        "Indian/Mauritius",
        "Indian/Reunion",
        "MET",
        "MST",
        "MST7MDT",
        "PST8PDT",
        "Pacific/Apia",
        "Pacific/Auckland",
        "Pacific/Bougainville",
        "Pacific/Chatham",
        "Pacific/Chuuk",
        "Pacific/Easter",
        "Pacific/Efate",
        "Pacific/Enderbury",
        "Pacific/Fakaofo",
        "Pacific/Fiji",
        "Pacific/Funafuti",
        "Pacific/Galapagos",
        "Pacific/Gambier",
        "Pacific/Guadalcanal",
        "Pacific/Guam",
        "Pacific/Honolulu",
        "Pacific/Kiritimati",
        "Pacific/Kosrae",
        "Pacific/Kwajalein",
        "Pacific/Majuro",
        "Pacific/Marquesas",
        "Pacific/Nauru",
        "Pacific/Niue",
        "Pacific/Norfolk",
        "Pacific/Noumea",
        "Pacific/Pago_Pago",
        "Pacific/Palau",
        "Pacific/Pitcairn",
        "Pacific/Pohnpei",
        "Pacific/Port_Moresby",
        "Pacific/Rarotonga",
        "Pacific/Tahiti",
        "Pacific/Tarawa",
        "Pacific/Tongatapu",
        "Pacific/Wake",
        "Pacific/Wallis",
        "WET",
      ],
      gender: ["male", "female"],
      roles: [],
      types: [
        {value:"regular-user",label:"Employee"}, 
        {value:"client",label:"Client"}
      ],
      langs: [
        {
          name: "German",
          value: 'de'
        },
        {
          name: "English",
          value: 'en'
        },
      ]
    };
  },
  methods: {
      changePage(page){
        this.queryParams.page = page;
        this.getResults();
      },
      clearQuery() {
      this.queryParams = {
        sort: [],
        filters: [],
        global_search: "",
      };
      this.search.name = "";
      this.search.email = "";
      this.search.role = "";
      this.search.created_at = "";
      this.search.lang = "";
      this.getResults();
    },
    multiSearch(target) {
      let typeSimple = false;
      this.queryParams.filters.forEach((item) => {
        if (item.type === "simple") {
          typeSimple = true;
        }
      });
      let found = false;
      if (typeSimple) {
        if (target == "name") {
          this.queryParams.filters.forEach((item) => {
            if (item.type === "simple" && item.name === "name") {
              item.text = this.search.name;
              found = true;
            }
          });
          if (!found) {
            this.queryParams.filters.push({
              type: "simple",
              name: "name",
              text: this.search.name,
            });
          }
        } else if (target == "email") {
          this.queryParams.filters.forEach((item) => {
            if (item.type === "simple" && item.name === "email") {
              item.text = this.search.email;
              found = true;
            }
          });
          if (!found) {
            this.queryParams.filters.push({
              type: "simple",
              name: "email",
              text: this.search.email,
            });
          }
        } else if (target == "role") {
          this.queryParams.filters.forEach((item) => {
            if (item.type === "simple" && item.name === "role") {
              item.text = this.search.role;
              found = true;
            }
          });
          if (!found) {
            this.queryParams.filters.push({
              type: "simple",
              name: "role",
              text: this.search.role,
            });
          }
        } else if (target == "lang") {
          this.queryParams.filters.forEach((item) => {
            if (item.type === "simple" && item.name === "lang") {
              item.text = this.search.lang;
              found = true;
            }
          });
          if (!found) {
            this.queryParams.filters.push({
              type: "simple",
              name: "lang",
              text: this.search.lang,
            });
          }
        } else if (target == "created_at") {
          this.queryParams.filters.forEach((item) => {
            if (item.type === "simple" && item.name === "created_at") {
              item.text = this.search.created_at;
              found = true;
            }
          });
          if (!found) {
            this.queryParams.filters.push({
              type: "simple",
              name: "created_at",
              text: this.search.created_at,
            });
          }
        }
      } else {
        if (target == "name") {
          this.queryParams.filters.push({
            type: "simple",
            name: "name",
            text: this.search.name,
          });
        } else if (target == "email") {
          this.queryParams.filters.push({
            type: "simple",
            name: "email",
            text: this.search.email,
          });
        } else if (target == "role") {
          this.queryParams.filters.push({
            type: "simple",
            name: "role",
            text: this.search.role,
          });
        } else if (target == "lang") {
          this.queryParams.filters.push({
            type: "simple",
            name: "lang",
            text: this.search.lang,
          });
        } else if (target == "created_at") {
          this.queryParams.filters.push({
            type: "simple",
            name: "created_at",
            text: this.search.created_at,
          });
        }
      }
      this.getResults();
      found = false;
      typeSimple = false;
    },

    resetFilter() {
      this.attribute_filter_data = [];
    },
    filter_users_by_attribute() {
      let data = {
        page: 1,
        filter: {
          id: this.attribute_filter.id,
          value: this.attribute_filter_data,
        },
      };
      this.$Progress.start();
      this.$store
        .dispatch("user/getEmployeesPaginated", data)
        .then((response) => {
          // this.users = response.data.data;
          if (this.excel_export) {
            window.open(
              this.$base_url +
                "/v-api/user/getEmployeesPaginated?page=1&export_excel=true&filter=" +
                encodeURI(
                  JSON.stringify({
                    id: this.attribute_filter.id,
                    value: this.attribute_filter_data,
                  })
                ),
              "_blank"
            );
          }

          this.$Progress.finish();
        })
        .catch((error) => {
          this.$Progress.fail();
        });
    },
    change_attribute(pindex) {
      this.user_selected_attributes[
        pindex
      ].attribute_id = this.attribute_selector[pindex].id;

      this.user_selected_attributes[pindex].type = this.attribute_selector[
        pindex
      ].type;
    },
    save_attributes() {
      let $this = this;
      this.$Progress.start();

      this.$store
        .dispatch("user/saveUserAttributes", {
          attributes: this.user_selected_attributes,
          user_id: this.form.id,
        })
        .then((response) => {
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message,
          });

          $this.getResults();
          $("#Modal").modal("hide");
        })
        .catch((error) => {
          this.$Progress.fail();
        });
    },
    increase_user_attributes() {
      this.user_selected_attributes.push({
        attribute_id: "",
        checkbox: [],
        radiobutton: [],
        dropdown: [],
        text: [],
      });
    },
    delete_user_attributes(index) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      })
        .then((result) => {
          if (result.value) {
            this.user_selected_attributes.splice(index, 1);
          }
        })
        .catch((error) => {});
    },
    getResults() {
      this.$Progress.start();
      this.$store
        .dispatch("user/getEmployeesPaginated", {
          queryParams: this.queryParams,
          page: this.queryParams.page,
        })
        .then((response) => {
          this.$Progress.finish();
        })
        .catch((error) => {
          this.$Progress.fail();
        });
    },
    newModal() {
      this.user_selected_attributes = [];

      this.editMode = false;
      this.form.reset();
      $("#Modal").modal("show");
    },
    editModal(item) {
      let $this = this;
      this.user_selected_attributes = [];
      item.attributes.forEach((attribute, index) => {

        this.attribute_selector[index] = this.attributes.filter(
          (attr) => attr.id == attribute.attribute_id
        )[0];

      if($this.attribute_selector[index] !== undefined)
      {
        $this.user_selected_attributes.push({
          attribute_id: attribute.attribute_id,
          [$this.attribute_selector[index].type]: attribute.value,
          type: $this.attribute_selector[index].type,
        });

      }


      });
      //   this.user_selected_attributes = item.attributes;
      this.editMode = true;
      this.form.reset();
      $("#Modal").modal("show");
      this.form.fill(item);
      // this.form.type = this.types.find(type => type.value = item.type);
      if (item.metadata) {
        this.metadata.fill(item.metadata);
      }
      this.metadata.user_id = item.id;

      this.form.roles = _.map(this.form.roles, function (value) {
        return {
          name: value.name,
        };
      });
    },
    createMetadata(data) {
      this.$Progress.start();
      this.$store
        .dispatch("user/createMetadata", data)
        .then((response) => {
          this.$Progress.finish();
          $("#Modal").modal("hide");
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
        })
        .catch((error) => {
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },
    editMetadata(data) {
      if (!data.id) {
        this.createMetadata(data);
        return false;
      }
      this.$Progress.start();
      this.$store
        .dispatch("user/editMetadata", data)
        .then((response) => {
          this.$Progress.finish();
          $("#Modal").modal("hide");
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
        })
        .catch((error) => {
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },

    saveForms() {
      const $this = this;
      this.$Progress.start();
      // this.form.type = this.form.type.value;
      this.$store
        .dispatch("user/createUser", this.form)
        .then((response) => {
          $("#Modal").modal("hide");

      $this.metadata.user_id = response.data.data.id;
      this.$Progress.start();
      this.$store
        .dispatch("user/createMetadata", $this.metadata)
        .then((response) => {
          this.$Progress.finish();
          this.getResults();
          $("#Modal").modal("hide");
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
        })
        .catch((error) => {
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });



          Toast.fire({
            type: "success",
            title: response.data.message,
          });
        })
        .catch((error) => {
          this.$Progress.fail();
          // this.form.type = this.types.find(type => type.value = this.form.type);
          console.log(error);
          Toast.fire({
            type: "error",
            title: error.response.data.message,
          });
        });




      
    },
    createUser() {
      this.$Progress.start();
      // this.form.type = this.form.type.value;
      this.$store
        .dispatch("user/createUser", this.form)
        .then((response) => {
          $("#Modal").modal("hide");
          this.$Progress.finish();
          this.getResults();
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
        })
        .catch((error) => {
          this.$Progress.fail();
          // this.form.type = this.types.find(type => type.value = this.form.type);
          Toast.fire({
            type: "error",
            title: error.response.data.message,
          });
        });
    },
    editUser(id) {
      this.form.roles = _.map(this.form.roles, function (value) {
        return {
          name: value.name,
        };
      });

      // this.form.type = this.form.type.value;
      this.form.id  = id
      this.$Progress.start();
      this.$store
        .dispatch('user/editUser', this.form)
        .then((response) => {
          $("#Modal").modal("hide");
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
          this.getResults();
        })
        .catch((error) => {
          this.$Progress.fail();
          // this.form.type = this.types.find(type => type.value = this.form.type);
          Toast.fire({
            type: "error",
            title: error.response.message,
          });
        });
    },
    getAllRoles() {
      roleApi
        .getAll()
        .then((response) => {
          this.roles = _.map(response.data.data, function (key, value) {
            return {
              id: key.id,
              name: key.name,
            };
          });
          this.$Progress.finish();
        })
        .catch((error) => {
          this.$Progress.fail();
        });
    },
    deleteUser(id) {
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
          this.$Progress.start();
          this.$store
          .dispatch('user/deleteUser', id)
            .then((response) => {
              this.$Progress.finish();
              Toast.fire({
                type: "success",
                title: response.data.message,
              });
            })
            .catch((error) => {
              this.$Progress.fail();
              Toast.fire({
                type: "error",
                title: "can't delete the role",
              });
            });
        }
      });
    },
    // attributes
    getUserAttributes() {
      this.$Progress.start();
      this.$store
        .dispatch("user/getUsersAttributes")
        .then((response) => {
          this.$Progress.finish();
        })
        .catch((error) => {
        });
    },
    // end of attributes
  },
  mounted() {
    this.getResults();
    this.getAllRoles();
    this.getUserAttributes();
  },
  computed: {
    ...mapGetters({
      attributes: "user/getUsersAttributes",
      users: "user/getEmployees",
    }),
  },
  beforeDestroy: function () {
    $("#Modal").modal("hide");
    $(".modal-backdrop").hide();
  },
  components: {
    quillEditor,
    DatePicker,
  },
};
</script>

<style scoped>
.col {
  padding-top: 2px;
  padding-bottom: 2px;
}
tbody tr:nth-of-type(odd) {
  background-color: #dcdcdc;
}

thead tr th {
  color: #2f5298 !important;
  font-size: 18px !important;
}
.nav-item{
    background-image: linear-gradient(180deg, white, dimgrey);
}
.tapStyle{
  margin-right:8px ;
}
.nav-link{
  color: black;
  font-weight: bold;
}

</style>
