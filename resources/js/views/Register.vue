<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-4">
        <div class="card login-card">
          <div class="card-header">
            <h4 class="login-title mb-0">{{ $t('registerFormTitle') }}</h4>
            <small class="login-title">{{ $t('registerFormDesc', 'registerFormDesc') }}</small>
          </div>
          <div class="card-body">
            <b-form @submit="registerNewUser">
              <b-form-group
                  id="fieldset-1"
                  :label=" $t('emailLabel', 'emailLabel')"
                  label-for="input-1"
                  class="login-label"
              >
                <b-form-input class="login-input" id="input-1" type="email" v-model="userEmail" trim required></b-form-input>
              </b-form-group>
              <b-form-group
                  id="fieldset-2"
                  :label=" $t('passwordLabel', 'passwordLabel')"
                  label-for="input-2"
                  class="login-label"
              >
                <b-form-input type="password" class="login-input" id="input-2" v-model="userPassword" trim required></b-form-input>
              </b-form-group>
              <b-form-group
                  id="fieldset-3"
                  :label=" $t('passwordConfirmationLabel', 'passwordConfirmationLabel')"
                  label-for="input-3"
                  class="login-label mb-0"
              >
                <b-form-input type="password" class="login-input" id="input-3" v-model="userPasswordConfirmation" trim required></b-form-input>
              </b-form-group>
              <div class="col-12 px-0">
                <b-button class="submit-btn mt-3 w-100" type="submit" variant="primary">{{ $t('registerBtnText', 'registerBtnText') }}</b-button>
              </div>
            </b-form>

            <!--<span @click="loginUser()" class="btn btn-primary submit-btn mt-3 px-4">{{ $t('loginText', 'loginText') }}</span>-->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      userEmail: null,
      userPassword: null,
      userPasswordConfirmation: null
    }
  },
  mounted() {

  },
  methods: {
    registerNewUser() {
      event.preventDefault()
      this.$store.dispatch('authStore/registerNewUser',
      {
        userEmail: this.userEmail,
        userPassword: this.userPassword
      }).then((response) => {
        if(response && response.status) {
          if(response.status === 'success') {
            this.userEmail = null
            this.userPassword = null
            this.userPasswordConfirmation = null
          }
        }
      })
    }
  }
}
</script>

<style scoped>
  .login-input {
    border-top: none;
    border-left: none;
    border-right: none;
    border-radius: 0px;
    padding-left: 5px;
    padding-right: 5px;
  }
  .login-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(88, 139, 139, 0.25);
    border-color: #588B8B;
  }
  .login-label {
    font-weight: 600;
    font-size: 14px;
    color: #588B8B;
  }
  .forgot-link {
    color: #588B8B;
  }
  .forgot-link:hover {
    font-weight: 600;
    cursor: pointer;
  }
  .login-title {
    font-weight: 600;
    color: #588B8B;
  }
  .card-header {
    background-color: #70BDBD26;
  }
  .login-card {
    border-radius: 0px;
    border-color: #65A2A282;
  }

  .submit-btn {
    border-radius: 0px;
    background-color: #588B8B;
    border: none;
    width: 100%;
  }
  .submit-btn:hover, .submit-btn:focus {
    background-color: #366060;
  }
  .submit-btn:active {
    background-color: #274f4f !important;
  }

</style>
