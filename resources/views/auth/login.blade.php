<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div id="app" class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-center mb-8">Login</h2>
            
            <form @submit.prevent="handleLogin" class="space-y-6">
                <!-- Alert untuk error -->
                <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    @{{ error }}
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Phone Number
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 py-2 text-gray-900 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md">
                            +62
                        </span>
                        <input 
                            type="number" 
                            v-model="form.phone"
                            class="w-full px-3 py-2 border border-gray-300 rounded-r-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                            @input="formatPhoneNumber"
                            placeholder="8123456789"
                        >
                    </div>
                    <small class="text-gray-500 mt-1">Contoh: 8123456789 (tanpa angka 0 di depan)</small>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Password
                    </label>
                    <input 
                        type="password" 
                        v-model="form.password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        v-model="form.remember"
                        class="h-4 w-4 text-blue-600"
                    >
                    <label class="ml-2 block text-gray-700 text-sm">
                        Ingat Saya
                    </label>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    @{{ loading ? 'Loading...' : 'Login' }}
                </button>

                <!-- Register Link -->
                <div class="text-center mt-4">
                    <p class="text-gray-600">
                        Belum punya akun? 
                        <a href="/register" class="text-blue-500 hover:text-blue-600 font-medium">
                            Daftar disini
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        const { createApp } = Vue

        createApp({
            data() {
                return {
                    form: {
                        number: '',
                        password: '',
                        remember: false
                    },
                    error: null,
                    loading: false
                }
            },
            methods: {
                async handleLogin() {
                    this.loading = true
                    this.error = null

                    try {
                        const response = await axios.post('/api/login', this.form)
                        
                        // Simpan token JWT ke localStorage
                        localStorage.setItem('token', response.data.token)
                        
                        // Redirect ke dashboard atau halaman utama
                        window.location.href = '/dashboard'
                    } catch (err) {
                        this.error = err.response?.data?.message || 'Terjadi kesalahan saat login'
                    } finally {
                        this.loading = false
                    }
                },
                formatPhoneNumber() {
                    // Hapus karakter non-digit
                    let number = this.form.phone.replace(/\D/g, '');
                    
                    // Hapus angka 0 di depan jika ada
                    if (number.startsWith('0')) {
                        number = number.substring(1);
                    }
                    
                    this.form.phone = number;
                }
            }
        }).mount('#app')

        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
</body>
</html>