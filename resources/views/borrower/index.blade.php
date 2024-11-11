<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Borrower Dashboard</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div id="app">
        <!-- Navbar -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <span class="text-xl font-semibold">P2P Lending</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">@{{ user.name }}</span>
                        <button @click="logout" class="text-red-500 hover:text-red-700">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Dashboard Peminjam</h2>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    @{{ error }}
                </div>

                <!-- Data Display -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Penghasilan Bulanan -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Penghasilan Bulanan</h3>
                        <p class="text-2xl font-bold text-green-600">
                            Rp @{{ formatCurrency(monthlyIncome) }}
                        </p>
                    </div>

                    <!-- Limit Pinjaman -->
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Limit Pinjaman (30%)</h3>
                        <p class="text-2xl font-bold text-blue-600">
                            Rp @{{ formatCurrency(loanLimit) }}
                        </p>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="mt-8 p-4 bg-yellow-50 rounded-lg">
                    <h3 class="font-semibold text-gray-700 mb-2">Informasi Penting</h3>
                    <ul class="list-disc list-inside text-gray-600 space-y-2">
                        <li>Limit pinjaman dihitung sebesar 30% dari penghasilan bulanan Anda</li>
                        <li>Pastikan untuk mempertimbangkan kemampuan pembayaran sebelum mengajukan pinjaman</li>
                        <li>Pembayaran dilakukan secara cicilan bulanan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue

        createApp({
            data() {
                return {
                    user: {},
                    monthlyIncome: 0,
                    loanLimit: 0,
                    loading: true,
                    error: null
                }
            },
            methods: {
                async fetchData() {
                    try {
                        const token = localStorage.getItem('token')
                        if (!token) {
                            window.location.href = '/'
                            return
                        }

                        const response = await axios.get('/api/borrower/loan-limit', {
                            headers: {
                                'Authorization': `Bearer ${token}`
                            }
                        })

                        this.user = response.data.user
                        this.monthlyIncome = response.data.monthly_income
                        this.loanLimit = response.data.loan_limit
                    } catch (err) {
                        this.error = 'Gagal memuat data. Silakan coba lagi.'
                        console.error(err)
                    } finally {
                        this.loading = false
                    }
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID').format(value)
                },
                async logout() {
                    try {
                        const token = localStorage.getItem('token')
                        await axios.post('/api/logout', {}, {
                            headers: {
                                'Authorization': `Bearer ${token}`
                            }
                        })
                        localStorage.removeItem('token')
                        window.location.href = '/'
                    } catch (err) {
                        console.error(err)
                    }
                }
            },
            mounted() {
                this.fetchData()
            }
        }).mount('#app')

        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    </script>
</body>
</html>
