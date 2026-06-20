<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
  Download, 
  ExternalLink, 
  User, 
  Users, 
  Calendar,
  FileDown
} from '@lucide/vue';
import CitechDashboardLayout from '@/components/CitechDashboardLayout.vue';

const props = defineProps({
    activeTimeline: Object,
    allTimelines: Array,
    userTeam: Object,
    teamMembers: Array,
});

const getFormattedDate = () => {
    const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    return new Date().toLocaleDateString('id-ID', options);
};

const mapTahap = (tahap) => {
    const mapping = {
        'pendaftaran_b1': 'Pendaftaran Batch 1',
        'pendaftaran_b2': 'Pendaftaran Batch 2',
        'penyisihan': 'Pengumpulan Berkas (Babak Penyisihan)',
        'final': 'Pengumpulan Berkas (Babak Final)',
        'awarding': 'Final & Awarding'
    };
    return mapping[tahap] || tahap;
};

const formatTimelineDate = (start, end) => {
    if (!start || !end) return '';
    const startD = new Date(start);
    const endD = new Date(end);
    const options = { day: 'numeric', month: 'long' };
    const yearOptions = { year: 'numeric' };
    
    const startStr = startD.toLocaleDateString('id-ID', options);
    const endStr = endD.toLocaleDateString('id-ID', { ...options, ...yearOptions });
    return `${startStr} - ${endStr}`;
};

const getTimelineStatus = (start, end) => {
    const now = new Date();
    const startD = new Date(start);
    const endD = new Date(end);

    if (now > endD) {
        return { label: 'Selesai', class: 'bg-green-100 text-green-700 border-green-200' };
    } else if (now >= startD && now <= endD) {
        return { label: 'Berlangsung', class: 'bg-amber-100 text-amber-700 border-amber-200' };
    } else {
        return { label: 'Akan Datang', class: 'bg-blue-50 text-blue-600 border-blue-100' };
    }
};

const getTimelineCircleClass = (start, end) => {
    const now = new Date();
    const startD = new Date(start);
    const endD = new Date(end);

    if (now > endD) {
        return 'bg-amber-500 border-amber-500';
    } else if (now >= startD && now <= endD) {
        return 'bg-amber-500 border-amber-500 ring-4 ring-amber-500/25';
    } else {
        return 'bg-white border-slate-300';
    }
};
</script>

<template>
    <Head title="Dashboard Peserta - CITECH 2026" />

    <CitechDashboardLayout activeMenu="dashboard" role="peserta">
        <template #header-title>
            <div class="hidden sm:block">
                <h2 class="text-lg font-black tracking-wide text-slate-800">Beranda</h2>
            </div>
        </template>

        <!-- Welcome section -->
        <div class="mb-8 space-y-1 animate-fade-in-up">
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">
                Selamat Datang, {{ $page.props.auth.user.name }}
            </h1>
            <p class="text-slate-400 text-xs md:text-sm font-bold tracking-wide">
                {{ getFormattedDate() }}
            </p>
        </div>

        <!-- Main Dashboard Cards Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Side (Downloads & Team Members) -->
            <div class="lg:col-span-5 space-y-8">
                
                <!-- Downloads Card -->
                <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-slate-100 space-y-6">
                    <h3 class="text-lg font-extrabold text-slate-800 tracking-tight flex items-center space-x-2">
                        <FileDown class="w-5 h-5 text-[#1e4d8c]" />
                        <span>Unduh Dokumen</span>
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Guidebook Download -->
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-slate-100/50 transition">
                            <div class="space-y-0.5">
                                <h4 class="text-sm font-bold text-slate-800">Guidebook Citech</h4>
                                <p class="text-[11px] font-bold text-slate-400">PDF</p>
                            </div>
                            <a 
                                href="#" 
                                class="flex items-center space-x-1 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl text-xs font-black transition border border-blue-100"
                            >
                                <span>Download</span>
                                <ExternalLink class="w-3.5 h-3.5" />
                            </a>
                        </div>

                        <!-- Surat Orisinalitas Download -->
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-slate-100/50 transition">
                            <div class="space-y-0.5">
                                <h4 class="text-sm font-bold text-slate-800">Surat Orisinalitas</h4>
                                <p class="text-[11px] font-bold text-slate-400">DOCX</p>
                            </div>
                            <a 
                                href="#" 
                                class="flex items-center space-x-1 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl text-xs font-black transition border border-blue-100"
                            >
                                <span>Download</span>
                                <ExternalLink class="w-3.5 h-3.5" />
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Anggota Tim Card -->
                <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-slate-100 space-y-6">
                    <h3 class="text-lg font-extrabold text-slate-800 tracking-tight flex items-center space-x-2">
                        <Users class="w-5 h-5 text-[#1e4d8c]" />
                        <span>Anggota Tim</span>
                    </h3>

                    <div v-if="userTeam" class="space-y-3">
                        <div class="p-2 bg-blue-50/50 border border-blue-100/50 rounded-2xl text-center mb-4">
                            <span class="text-xs font-black tracking-wider text-blue-800 uppercase">TIM: {{ userTeam.nama_tim }}</span>
                        </div>
                        
                        <div 
                            v-for="member in teamMembers" 
                            :key="member.id_member" 
                            class="flex items-center space-x-4 p-3.5 bg-slate-50 rounded-xl border border-slate-100/80 hover:bg-slate-100/50 transition"
                        >
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                                {{ member.nama_peserta.charAt(0).toUpperCase() }}
                            </div>
                            <div class="flex-grow">
                                <h4 class="text-sm font-extrabold text-slate-800">{{ member.nama_peserta }}</h4>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">NIM. {{ member.nim_peserta }}</p>
                            </div>
                            <span 
                                class="text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full border"
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
                    
                    <!-- Empty State -->
                    <div v-else class="text-center py-8 space-y-4">
                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                            <Users class="w-6 h-6" />
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-slate-700">Kamu belum terdaftar di tim mana pun</p>
                            <p class="text-[11px] font-bold text-slate-400">Silakan buat tim baru untuk mulai berpartisipasi.</p>
                        </div>
                        <Link 
                            :href="route('peserta.tim')" 
                            class="inline-flex items-center space-x-2 px-5 py-2.5 bg-[#1e4d8c] hover:bg-[#153a6b] text-white rounded-xl text-xs font-black transition shadow-sm"
                        >
                            <span>Buat Tim Sekarang</span>
                        </Link>
                    </div>
                </div>

            </div>

            <!-- Right Side (Timeline) -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-slate-100 space-y-8">
                    <h3 class="text-lg font-extrabold text-slate-800 tracking-tight flex items-center space-x-2">
                        <Calendar class="w-5 h-5 text-[#1e4d8c]" />
                        <span>Timeline Perlombaan</span>
                    </h3>

                    <!-- Timeline flow -->
                    <div class="relative pl-8 space-y-8">
                        <!-- Vertical line -->
                        <div class="absolute left-[11px] top-2 bottom-2 w-0.5 bg-slate-200"></div>

                        <!-- Timeline Item -->
                        <div 
                            v-for="item in allTimelines" 
                            :key="item.id_timeline"
                            class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-2"
                        >
                            <!-- Circle marker -->
                            <div 
                                class="absolute -left-[28px] top-1.5 w-6 h-6 rounded-full border-4 flex items-center justify-center transition-all duration-300"
                                :class="getTimelineCircleClass(item.tanggal_mulai, item.tanggal_selesai)"
                            >
                            </div>

                            <!-- Content -->
                            <div class="space-y-1">
                                <h4 class="text-sm font-extrabold text-slate-800 tracking-tight">
                                    {{ mapTahap(item.tahap) }}
                                </h4>
                                <p class="text-slate-400 text-xs font-semibold">
                                    {{ formatTimelineDate(item.tanggal_mulai, item.tanggal_selesai) }}
                                </p>
                            </div>

                            <!-- Status Pill -->
                            <div>
                                <span 
                                    class="inline-block text-[10px] font-black uppercase tracking-wider px-3 py-1.5 rounded-xl border"
                                    :class="getTimelineStatus(item.tanggal_mulai, item.tanggal_selesai).class"
                                >
                                    {{ getTimelineStatus(item.tanggal_mulai, item.tanggal_selesai).label }}
                                </span>
                            </div>
                        </div>
                    </div>
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
    animation: fadeInUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
