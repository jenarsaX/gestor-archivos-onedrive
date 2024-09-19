<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">{{ __('Register') }}</div>

          <div class="card-body">
            <form @submit.prevent="register">
              <div class="row mb-3">
                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                <div class="col-md-6">
                  <input 
                    id="name" 
                    type="text" 
                    v-model="name" 
                    class="form-control"
                    :class="{'is-invalid': errors.name}" 
                    required 
                    autofocus 
                  />
                  <span v-if="errors.name" class="invalid-feedback">
                    <strong>{{ errors.name[0] }}</strong>
                  </span>
                </div>
              </div>

              <div class="row mb-3">
                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                <div class="col-md-6">
                  <input 
                    id="email" 
                    type="email" 
                    v-model="email" 
                    class="form-control"
                    :class="{'is-invalid': errors.email}" 
                    required 
                  />
                  <span v-if="errors.email" class="invalid-feedback">
                    <strong>{{ errors.email[0] }}</strong>
                  </span>
                </div>
              </div>

              <div class="row mb-3">
                <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                <div class="col-md-6">
                  <input 
                    id="password" 
                    type="password" 
                    v-model="password" 
                    class="form-control"
                    :class="{'is-invalid': errors.password}" 
                    required 
                  />
                  <span v-if="errors.password" class="invalid-feedback">
                    <strong>{{ errors.password[0] }}</strong>
                  </span>
                </div>
              </div>

              <div class="row mb-3">
                <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                <div class="col-md-6">
                  <input 
                    id="password-confirm" 
                    type="password" 
                    v-model="passwordConfirmation" 
                    class="form-control" 
                    required 
                  />
                </div>
              </div>

              <div class="row mb-0">
                <div class="col-md-6 offset-md-4">
                  <button type="submit" class="btn btn-primary">
                    {{ __('Register') }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      passwordConfirmation: '',
      errors: {}
    };
  },
  methods: {
    async register() {
      this.errors = {}; // Reset errors

      try {
        await axios.post('/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirmation
        });

        // Redirect or handle successful registration
        window.location.href = '/login'; // Or another page
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors;
        } else {
          console.error(error);
        }
      }
    }
  }
};
</script>

<style scoped>
/* Add custom styles if necessary */
</style>
