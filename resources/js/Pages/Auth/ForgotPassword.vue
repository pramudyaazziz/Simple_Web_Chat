<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="col-md-6 p-5 align-items-center d-none d-md-flex">
            <img :src="image" alt="welcome" class="img-fluid">
        </div>
        <div class="col-md-6 p-4 d-flex align-items-center">
            <div class="card shadow w-100">
                <div class="card-body d-flex align-items-center">
                    <div class="col p-4">
                        <h5>Reset</h5>
                        <h3 class="text-primary fw-bold">Password</h3>
                        <hr>
                        <span class="text-muted">Forgot your password? <br> No problem. Just let us know your email address and we will email you a password reset
                            link that will allow you to make new password.</span>
                        <form @submit.prevent="submit">
                            <div class="mb-3 mt-4">
                                <label class="form-label">Email address</label>
                                <input type="email" autofocus :class="[{ 'is-invalid': form.errors.email, 'form-control': true, 'is-valid': status }]" v-model="form.email">
                                <div class="invalid-feedback">{{ form.errors.email }}</div>
                                <div class="valid-feedback">
                                    Reset password link has been sent to <strong>{{ status }}</strong> And Make sure to check spam section as well!
                                </div>
                            </div>
                            <div class="row ps-2 pe-2">
                                <button type="submit" :class="[{ 'btn': true, 'btn-primary': true, 'loading': form.processing }]">
                                    <div v-if="form.processing">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Reset
                                    </div>
                                    <span v-else>Reset</span>
                                </button>
                            </div>
                        </form>
                        <div class="mt-4">
                            <p class="text-center">Have remembered your password? <Link :href="route('login')">Login</Link></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script>
    import GuestLayout from '@/Layouts/GuestLayout.vue'
    import { Head, useForm, Link } from '@inertiajs/vue3';

    export default {
        components: {
            GuestLayout,
            Link,
            Head
        },
        data() {
            return {
                image: window.location.origin + '/assets/auth/img/forgot.svg',
                form: useForm({
                    email: ''
                }),
                status: ""
            }
        },
        methods: {
            submit() {
                this.form.post(route('password.email'), {
                    onFinish: () => {
                        this.form.reset('email')
                        if (! this.form.errors.email) {
                            this.status = `${this.form.email}`
                        }
                    },
                });
                if (! this.form.errors.email) {
                    this.status = ""
                }
            }
        }
    }

</script>
