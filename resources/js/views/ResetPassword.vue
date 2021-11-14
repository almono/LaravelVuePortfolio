<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-4">
        <div class="card login-card">
          <div class="card-header">
            <h4 class="login-title mb-0">{{ $t('resetPasswordTitle') }}</h4>
          </div>
          <div class="card-body">

            <div v-if="invalidReset" class="col-12 px-0">
              <span>{{ $t(errorMsg, errorMsg) }}</span>
            </div>
            <div v-else-if="successReset">
              <template v-if="!passwordChanged">
                <span style="color: gray;" class="mb-2">{{ $t('setNewPasswordText', 'setNewPasswordText') }}</span>
                <b-form @submit="submitNewPassword">
                  <b-form-group
                      id="fieldset-1"
                      :label=" $t('newPasswordLabel', 'newPasswordLabel')"
                      label-for="input-1"
                      class="login-label mb-0"
                  >
                    <b-form-input type="password" class="login-input" id="input-1" v-model="newPassword" trim required></b-form-input>
                  </b-form-group>
                  <b-form-group
                      id="fieldset-2"
                      :label=" $t('confirmPasswordLabel', 'confirmPasswordLabel')"
                      label-for="input-2"
                      class="login-label mb-0"
                  >
                    <b-form-input type="password" class="login-input" id="input-2" v-model="newPasswordConfirm" trim required></b-form-input>
                  </b-form-group>

                  <div class="col-12 px-0" v-if="wrongPasswords">
                    <span>{{ $t('passwordsNotMatchingWarning', 'passwordsNotMatchingWarning') }}</span>
                  </div>
                  <div class="col-12 px-0" v-if="emptyFields">
                    <span>{{ $t('inputsNotFilledWarning', 'inputsNotFilledWarning') }}</span>
                  </div>
                  <div class="col-12 px-0">
                    <b-button class="submit-btn mt-3 w-100" type="submit" variant="primary">{{ $t('sendNewPasswordBtn', 'sendNewPasswordBtn') }}</b-button>
                  </div>
                </b-form>
              </template>
              <template v-else>
                <p>{{ $t('passwordHasBeenChangedConfirmation', 'passwordHasBeenChangedConfirmation') }}</p>
                <router-link class="forgot-link" :to="{ name: 'Login'}">{{ $t('backToLoginLink', 'backToLoginLink') }}</router-link>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ResetPassword',
  data() {
    return {
      resetInvalid: false,
      successReset: false,
      newPassword: null,
      newPasswordConfirm: null,
      wrongPasswords: false,
      emptyFields: false,
      passwordChanged: false,
      errorMsg: null,
      userId: this.$route.query.user ? this.$route.query.user : null,
      userToken: this.$route.query.token ? this.$route.query.token : null
    }
  },
  mounted() {
    console.log(this.$route)
    this.resetPassword()
  },
  methods: {
    resetPassword() {
      if(this.userId && this.userToken) {
        this.$store.dispatch('authStore/resetPassword',
        {
          userId: this.userId,
          userToken: this.userToken
        }).then((response) => {

          if(response && response.status) {
            if(response.status === 'success') {
              this.successReset = true
              this.invalidReset = false
            } else {
              this.invalidReset = true
              this.errorMsg = response.message
            }
          }
        })
      } else {
        this.resetInvalid = true
      }
    },
    submitNewPassword(event) {
      event.preventDefault()
      this.wrongPasswords = false
      this.emptyFields = false

      if(this.newPassword && this.newPasswordConfirm) {
        if(this.newPassword === this.newPasswordConfirm) {
          this.$store.dispatch('authStore/setNewPassword',
          {
            newPassword: this.newPassword,
            userId: this.userId
          }).then((response) => {
            if(response && response.status) {
              if(response.status === 'success') {
                this.passwordChanged = true;
              }
            }
          })
        } else {
          this.wrongPasswords = true
        }
      } else {
        this.emptyFields = true
      }
    }
  },
  computed: {
    invalidReset() {
      return this.resetInvalid
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
    width: 100%;
    text-align: center;
  }
</style>
