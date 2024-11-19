<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="max-w-md mx-auto mt-12 p-6 bg-white rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
                Bem-vindo de volta!
            </h1>

            <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mb-4">
                    <InputLabel for="password" value="Senha" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mb-4 flex items-center justify-between">
                    <label class="flex items-center text-sm text-gray-600">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ml-2">Lembrar-me</span>
                    </label>

                    <!-- <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-indigo-600 hover:text-indigo-800"
                    >
                        Esqueceu sua senha?
                    </Link> -->
                </div>

                <div class="flex justify-center">
                    <PrimaryButton
                        class="w-full bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 transition"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        Entrar
                    </PrimaryButton>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Não tem uma conta?
                    <Link
                        :href="route('register')"
                        class="font-semibold text-indigo-600 hover:text-indigo-800"
                    >
                        Registre-se agora
                    </Link>
                </p>
            </div>
        </div>
    </GuestLayout>
</template>
