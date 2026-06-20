<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Users, Info, ShieldAlert } from '@lucide/vue';
import CitechDashboardLayout from '@/components/CitechDashboardLayout.vue';

const props = defineProps({
    userTeam: Object,
    teamMembers: Array,
});
</script>

<template>
    <Head title="Manajemen Tim - CITECH 2026" />

    <CitechDashboardLayout activeMenu="peserta.tim" role="peserta">
        <template #header-title>
            <h2 class="text-lg font-black tracking-wide text-slate-800">Manajemen Tim</h2>
        </template>

        <div class="space-y-8 animate-fade-in-up">
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-slate-100 space-y-6">
                <div class="flex items-center space-x-3">
                    <Users class="w-6 h-6 text-[#1e4d8c]" />
                    <h3 class="text-xl font-extrabold text-slate-800">Detail Tim Anda</h3>
                </div>

                <div v-if="userTeam" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Nama Tim</span>
                            <p class="text-base font-extrabold text-slate-800">{{ userTeam.nama_tim }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Universitas / Institusi</span>
                            <p class="text-base font-extrabold text-slate-800">{{ userTeam.universitas }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Status Seleksi</span>
                            <div>
                                <span class="inline-block text-xs font-black uppercase tracking-wider px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ userTeam.status_seleksi }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Batch Pendaftaran</span>
                            <p class="text-base font-extrabold text-slate-800">Batch {{ userTeam.batch || '1' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Daftar Anggota</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div 
                                v-for="member in teamMembers" 
                                :key="member.id_member" 
                                class="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex flex-col justify-between min-h-[120px]"
                            >
                                <div>
                                    <h5 class="text-sm font-extrabold text-slate-800">{{ member.nama_peserta }}</h5>
                                    <p class="text-[11px] font-bold text-slate-400 mt-0.5">NIM. {{ member.nim_peserta }}</p>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <span 
                                        class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded border"
                                        :class="[
                                            member.role === 'ketua' 
                                                ? 'bg-amber-50 text-amber-700 border-amber-200' 
                                                : 'bg-slate-100 text-slate-600 border-slate-200'
                                        ]"
                                    >
                                        {{ member.role }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-12 space-y-4 max-w-md mx-auto">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                        <Users class="w-8 h-8" />
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-lg font-bold text-slate-800">Belum Ada Data Tim</h4>
                        <p class="text-xs font-bold text-slate-400 leading-relaxed">
                            Anda belum tergabung atau mendaftarkan kelompok tim Anda. Silakan isi formulir pembuatan tim baru.
                        </p>
                    </div>
                    <button class="px-6 py-3 bg-[#1e4d8c] hover:bg-[#153a6b] text-white font-bold text-sm rounded-xl transition shadow-md hover:shadow-lg hover:shadow-blue-500/10">
                        Buat Tim Baru
                    </button>
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
