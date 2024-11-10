<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div id="app" class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-center mb-8">Register</h2>
            
            <form @submit.prevent="handleRegister" class="space-y-6">
                <!-- Alert untuk error -->
                <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    @{{ error }}
                </div>

                <!-- Nama -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Name
                    </label>
                    <input 
                        type="text" 
                        v-model="form.name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Phone Number
                    </label>
                    <input 
                        type="number" 
                        v-model="form.phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Identity Number
                    </label>
                    <input 
                        type="number" 
                        v-model="form.identity_number"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        KTP Photo
                    </label>
                    <input 
                        type="file" 
                        v-model="form.ktp_photo"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Monthly Income
                    </label>
                    <input 
                        type="number" 
                        v-model="form.monthly_income"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Role
                    </label>
                    <select 
                        v-model="form.role"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="" disabled>Pilih Role</option>
                        <option value="lender">Lender</option>
                        <option value="borrower">Borrower</option>
                    </select>
                </div>
                <div v-if="form.role === 'lender'">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        NPWP
                    </label>
                    <input 
                        type="number" 
                        v-model="form.npwp"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
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

                <!-- Konfirmasi Password -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Konfirmasi Password
                    </label>
                    <input 
                        type="password" 
                        v-model="form.password_confirmation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                </div>

                <!-- Register Button -->
                <button 
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    @{{ loading ? 'Loading...' : 'Register' }}
                </button>

                <!-- Login Link -->
                <div class="text-center mt-4">
                    <p class="text-gray-600">
                        Sudah punya akun? 
                        <a href="/" class="text-blue-500 hover:text-blue-600 font-medium">
                            Login disini
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
                        name: '',
                        phone: '',
                        identity_number: '',
                        ktp_photo: '',
                        monthly_income: '',
                        role: '',
                        npwp: '',
                        password: '',
                        password_confirmation: ''
                    },
                    error: null,
                    loading: false
                }
            },
            methods: {
                async handleRegister() {
                    this.loading = true
                    this.error = null

                    try {
                        const response = await axios.post('/api/register', this.form)
                        
                        // Simpan token JWT ke localStorage
                        localStorage.setItem('token', response.data.token)
                        
                        // Redirect ke dashboard
                        window.location.href = '/dashboard'
                    } catch (err) {
                        this.error = err.response?.data?.message || 'Terjadi kesalahan saat registrasi'
                    } finally {
                        this.loading = false
                    }
                }
            }
        }).mount('#app')

        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
</body>
</html> 