<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lender Dashboard</title>
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
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-8">
                    <!-- Total Investment Card -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Total Investasi</h2>
                        <p class="text-3xl font-bold text-green-600">
                            Rp @{{ formatCurrency(totalInvestment) }}
                        </p>
                    </div>

                    <!-- New Investment Form -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4">Investasi Baru</h2>
                        <form @submit.prevent="submitInvestment" class="space-y-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Jumlah Investasi
                                </label>
                                <input 
                                    type="number" 
                                    v-model="newInvestment.amount"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                    required
                                    min="100000"
                                    step="10000"
                                >
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Bank Tujuan
                                </label>
                                <select 
                                    v-model="newInvestment.bank_code"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                    required
                                >
                                    <option value="" disabled>Pilih Bank</option>
                                    <option value="1123">BCA</option>
                                    <option value="1124">BRI</option>
                                    <option value="1125">BNI</option>
                                    <option value="1126">Mandiri</option>
                                </select>
                            </div>

                            <button 
                                type="submit"
                                :disabled="submitting"
                                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md"
                            >
                                @{{ submitting ? 'Processing...' : 'Submit Investasi' }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Column - Investment History -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Riwayat Investasi</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bank</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. VA</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="investment in investmentHistory" :key="investment.id">
                                    <td class="px-6 py-4">@{{ formatDate(investment.created_at) }}</td>
                                    <td class="px-6 py-4">Rp @{{ formatCurrency(investment.amount) }}</td>
                                    <td class="px-6 py-4">@{{ investment.bank_name }}</td>
                                    <td class="px-6 py-4">@{{ investment.virtual_account }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal untuk menampilkan Virtual Account -->
        <div v-if="showVAModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-8 rounded-lg max-w-md w-full">
                <h3 class="text-xl font-bold mb-4">Virtual Account Generated</h3>
                <p class="text-gray-600 mb-2">Silakan transfer ke nomor Virtual Account:</p>
                <p class="text-2xl font-bold text-blue-600 mb-4">@{{ currentVA }}</p>
                <p class="text-gray-600 mb-4">Jumlah: Rp @{{ formatCurrency(currentAmount) }}</p>
                <button 
                    @click="closeVAModal"
                    class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue

        createApp({
            data() {
                return {
                    user: {},
                    totalInvestment: 0,
                    investmentHistory: [],
                    loading: true,
                    submitting: false,
                    showVAModal: false,
                    currentVA: '',
                    currentAmount: 0,
                    newInvestment: {
                        amount: '',
                        bank_code: ''
                    },
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

                        const response = await axios.get('/api/lender/investment-data', {
                            headers: {
                                'Authorization': `Bearer ${token}`
                            }
                        })

                        this.user = response.data.user
                        this.totalInvestment = response.data.total_investment
                        this.investmentHistory = response.data.investment_history
                    } catch (err) {
                        this.error = 'Gagal memuat data'
                        console.error(err)
                    } finally {
                        this.loading = false
                    }
                },
                async submitInvestment() {
                    this.submitting = true
                    try {
                        const token = localStorage.getItem('token')
                        const response = await axios.post('/api/lender/investments', this.newInvestment, {
                            headers: {
                                'Authorization': `Bearer ${token}`
                            }
                        })

                        // Show VA in modal
                        this.currentVA = response.data.investment.virtual_account
                        this.currentAmount = response.data.investment.amount
                        this.showVAModal = true

                        // Reset form
                        this.newInvestment.amount = ''
                        this.newInvestment.bank_code = ''

                        // Refresh data
                        await this.fetchData()
                    } catch (err) {
                        alert(err.response?.data?.message || 'Terjadi kesalahan')
                    } finally {
                        this.submitting = false
                    }
                },
                closeVAModal() {
                    this.showVAModal = false
                    this.currentVA = ''
                    this.currentAmount = 0
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID').format(value)
                },
                formatDate(date) {
                    return new Date(date).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })
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
