<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { User, Settings, Lock } from '@lucide/vue';
import CitechDashboardLayout from '@/components/CitechDashboardLayout.vue';
</script>

<template>
    <Head title="Profil Pengguna - CITECH 2026" />

    <CitechDashboardLayout activeMenu="peserta.profil" role="peserta">
        <template #header-title>
            <h2 class="text-lg font-black tracking-wide text-slate-800">Profil Saya</h2>
        </template>

        <div class="space-y-8 animate-fade-in-up">
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-slate-100 space-y-6">
                
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                        <User class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-800">{{ $page.props.auth.user.name }}</h3>
                        <p class="text-xs font-bold text-slate-400">Peserta Citech 2026</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Alamat Email</span>
                            <span class="text-sm font-extrabold text-slate-800">{{ $page.props.auth.user.email }}</span>
                        </div>
                        
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Tanggal Pendaftaran Akun</span>
                            <span class="text-sm font-extrabold text-slate-800">
                                {{ new Date($page.props.auth.user.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Settings Redirection Section -->
                <div class="border-t border-slate-100 pt-6 flex flex-wrap gap-4">
                    <Link 
                        :href="route('profile.edit')" 
                        class="inline-flex items-center space-x-2 px-5 py-3 bg-[#1e4d8c] hover:bg-[#153a6b] text-white rounded-xl text-xs font-black transition shadow-sm"
                    >
                        <Settings class="w-4 h-4" />
                        <span>Edit Profil</span>
                    </Link>

                    <Link 
                        :href="route('security.edit')" 
                        class="inline-flex items-center space-x-2 px-5 py-3 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-black transition shadow-sm"
                    >
                        <Lock class="w-4 h-4" />
                        <span>Ubah Password & Keamanan</span>
                    </Link>
                </div>

            </div>
        </div>
    </CitechDashboardLayout>
</template>

<style scoped>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
