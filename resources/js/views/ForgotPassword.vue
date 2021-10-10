<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-4">
        <div class="card login-card">
          <div class="card-header">
            <h4 class="login-title mb-0">{{ $t('forgotPasswordFormTitle') }}</h4>
            <small class="login-title">{{ $t('forgotPasswordDesc', 'forgotPasswordDesc') }}</small>
          </div>
          <div class="card-body">
            <b-form-group
                id="fieldset-1"
                :label=" $t('emailLabel', 'emailLabel')"
                label-for="input-1"
                class="login-label mb-0"
            >
              <b-form-input class="login-input" id="input-1" v-model="userEmail" trim></b-form-input>
            </b-form-group>

            <div class="col-12 px-0 mb-2">
              <small class="forgot-link">
                <router-link class="forgot-link" :to="{ name: 'Login'}">{{ $t('backToLoginLink', 'backToLoginLink') }}</router-link>
              </small>
            </div>

            <span @click="forgotPassword()" class="btn btn-primary forgot-submit-btn mt-3 px-4">{{ $t('sendForgotPassEmail', 'sendForgotPassEmail') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ForgotPassword',
  data() {
    return {
      userEmail: null,
    }
  },
  mounted() {

  },
  methods: {
    forgotPassword() {
      this.$store.dispatch('authStore/forgotPassword',
      {
        userEmail: this.userEmail
      }).then((response) => {
        console.log('test')
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

  .forgot-submit-btn {
    border-radius: 0px;
    background-color: #588B8B;
    border: none;
    width: 200px;
  }
  .forgot-submit-btn:hover, .forgot-submit-btn:focus {
    background-color: #366060;
  }
  .forgot-submit-btn:active {
    background-color: #274f4f !important;
  }

</style>
