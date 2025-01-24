<template>
  <div class="container-fluid players">
      <div class="row justify-content-center">
          <div class="col-md-12">
              <div class="card">
                  <div class="card-body" id="vtable">
                      <el-row :gutter="20" type="flex" justify="space-between" align="middle">
                          <el-col :lg="4" :md="4" :sm="6" :xs="12">
                            <b-form-checkbox v-model="target" name="check-button" switch>
                              Search for <span v-if="target === false">Fingerprint</span><span v-if="target === true">Nickname</span>:
                            </b-form-checkbox>
                          </el-col>
                          <el-col :lg="4" :md="4" :sm="6" :xs="12" v-if="target === false">
                              <el-input :disabled="target === true" v-model="fingerprint" placeholder="Fingerprint"></el-input>
                          </el-col>
                          <el-col :lg="4" :md="4" :sm="6" :xs="12" v-if="target === true">
                              <el-input :disabled="target === false" v-model="nickname" placeholder="Nickname"></el-input>
                          </el-col>
                          <el-col :lg="2" :md="2" :sm="6" :xs="12">
                            <b-button @click="doSearch" type="submit" variant="primary">Search</b-button>
                          </el-col>
                      </el-row>
                      <hr />
                      <el-row v-if="result !== null" :gutter="20" type="flex" justify="space-between">
                         <el-col :lg="6" :md="6" :sm="12" :xs="12">
                            <p><b>Shoutbox Messages:</b></p>
                            <ul>
                              <li v-for="item in result['sb']">
                                {{ item }}
                              </li>
                            </ul>
                          </el-col>
                          <el-col :lg="6" :md="6" :sm="12" :xs="12">
                            <p><b>Registrations:</b></p>
                            <ul>
                              <li v-for="item in result['reg']">
                                {{ item }}
                              </li>
                            </ul>
                          </el-col>
                      </el-row>
                   </div>
              </div>
          </div>
      </div>
   </div>
</template>
<script>
    export default {
        data() {
          return {
            nickname: null,
            fingerprint: null,
            target: false,
            result: null,
          };
        },
        mounted() {

        },
        methods: {
          doSearch(evt) {
              this.result = null
              evt.preventDefault()
              evt.stopPropagation()
              let data = new FormData()
              if(this.target === true) data.append('nickname', this.nickname)
              else data.append('fp', this.fingerprint)
              axios.post('/fpnicksearch', data)
              .then(response => {
                  if(response.data.success === true){
                      this.result = response.data.result
                  }else{
                      console.log(response.data)
                  }
              }, (error) => {
                  console.log(error)
              });
          }
        }
    }
</script>