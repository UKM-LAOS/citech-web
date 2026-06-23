<script setup>
import { Head } from '@inertiajs/vue3';
import {
    Search,
    Users,
    Award,
    ShieldCheck,
    Info,
    FileText,
    CheckCircle2,
    X,
} from '@lucide/vue';
import { ref, computed } from 'vue';
import CitechDashboardLayout from '@/components/CitechDashboardLayout.vue';

const props = defineProps({
    teams: Array,
});

const searchQuery = ref('');
const selectedTeamDetails = ref(null);
const isDetailModalOpen = ref(false);

// Filter teams by search query
const filteredTeams = computed(() => {
    if (!props.teams) {
return [];
}

    return props.teams.filter((team) => {
        const query = searchQuery.value.toLowerCase();

        return (
            team.nama_tim.toLowerCase().includes(query) ||
            team.universitas.toLowerCase().includes(query)
        );
    });
});

const formatDate = (dateStr) => {
    if (!dateStr) {
return '-';
}

    const options = {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    };

    return new Date(dateStr).toLocaleDateString('id-ID', options);
};

const mapStatusSeleksi = (status) => {
    const mapping = {
        penyisihan: 'Babak Penyisihan',
        tidak_lolos_final: 'Tidak Lolos Final',
        final: 'Babak Final',
    };

    return mapping[status] || status;
};

const getKetuaName = (members) => {
    if (!members) {
return '-';
}

    const ketua = members.find((m) => m.role === 'ketua');

    return ketua ? ketua.nama_peserta : '-';
};

const openTeamDetails = (team) => {
    // Sort members: leader first
    const membersList = team.members
        ? [...team.members].sort((a, b) => {
              if (a.role === 'ketua') {
return -1;
}

              if (b.role === 'ketua') {
return 1;
}

              return a.id_member - b.id_member;
          })
        : [];

    selectedTeamDetails.value = {
        ...team,
        sortedMembers: membersList,
    };
    isDetailModalOpen.value = true;
};

const closeTeamDetails = () => {
    isDetailModalOpen.value = false;
};
</script>

<template>
    <Head title="Tim Terdaftar - CITECH 2026" />

    <CitechDashboardLayout activeMenu="admin.tim-terdaftar" role="admin">
        <template #header-title>
            <h2
                class="font-sans text-lg font-black tracking-wide text-slate-800"
            >
                Tim Terdaftar
            </h2>
        </template>

        <div class="animate-fade-in-up space-y-6">
            <!-- Header Card -->
            <div
                class="flex flex-col justify-between gap-6 rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_10px_35px_rgba(0,0,0,0.03)] md:flex-row md:items-center md:p-8"
            >
                <div class="space-y-2">
                    <h3
                        class="flex items-center space-x-2 text-xl font-black tracking-tight text-slate-800"
                    >
                        <CheckCircle2 class="h-6 w-6 text-green-600" />
                        <span>Daftar Tim Terverifikasi</span>
                    </h3>
                    <p
                        class="max-w-2xl text-xs leading-relaxed font-bold text-slate-500"
                    >
                        Menampilkan daftar seluruh tim peserta CITECH 2026 yang
                        telah berhasil melewati verifikasi berkas persyaratan
                        administratif dan penyelesaian pembayaran pendaftaran.
                    </p>
                </div>

                <!-- Search Input -->
                <div class="relative w-full flex-shrink-0 md:w-80">
                    <span
                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
                    >
                        <Search class="h-4 w-4" />
                    </span>
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Cari nama tim atau universitas..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pr-4 pl-10 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-blue-900 focus:outline-none"
                    />
                </div>
            </div>

            <!-- Table Card -->
            <div
                class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_10px_35px_rgba(0,0,0,0.03)]"
            >
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr
                                class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                <th class="px-6 py-4">Nama Tim / Instansi</th>
                                <th class="px-6 py-4">Ketua Tim</th>
                                <th class="px-6 py-4">Batch Lomba</th>
                                <th class="px-6 py-4">Tanggal Verifikasi</th>
                                <th class="px-6 py-4 text-center">
                                    Status Lomba
                                </th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-slate-100 text-xs font-bold text-slate-700"
                        >
                            <tr v-if="filteredTeams.length === 0">
                                <td
                                    colspan="6"
                                    class="py-12 text-center font-bold text-slate-400"
                                >
                                    Tidak ada data tim terdaftar yang ditemukan.
                                </td>
                            </tr>
                            <tr
                                v-for="team in filteredTeams"
                                :key="team.id_tim"
                                class="transition-colors hover:bg-slate-50/30"
                            >
                                <!-- Team / Univ -->
                                <td class="space-y-1 px-6 py-4">
                                    <div
                                        class="text-sm font-extrabold text-blue-900"
                                    >
                                        {{ team.nama_tim }}
                                    </div>
                                    <div
                                        class="text-[10px] font-bold tracking-wider text-slate-400 uppercase"
                                    >
                                        {{ team.universitas }}
                                    </div>
                                </td>

                                <!-- Ketua Tim -->
                                <td
                                    class="px-6 py-4 font-semibold text-slate-600"
                                >
                                    {{ getKetuaName(team.members) }}
                                </td>

                                <!-- Batch -->
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-block rounded-lg bg-slate-100 px-2.5 py-1 text-[10px] font-black tracking-wider text-slate-600 uppercase"
                                    >
                                        Batch {{ team.batch || 1 }}
                                    </span>
                                </td>

                                <!-- Verification Date -->
                                <td
                                    class="px-6 py-4 font-semibold text-slate-500"
                                >
                                    {{
                                        team.pembayaran
                                            ? formatDate(
                                                  team.pembayaran.uploaded_at,
                                              )
                                            : '-'
                                    }}
                                </td>

                                <!-- Selection Status -->
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-block rounded-full border px-3 py-1 text-[9px] font-black tracking-wider uppercase shadow-sm"
                                        :class="[
                                            team.status_seleksi === 'final'
                                                ? 'border-green-200 bg-green-50 text-green-600'
                                                : team.status_seleksi ===
                                                    'tidak_lolos_final'
                                                  ? 'border-red-200 bg-red-50 text-red-600'
                                                  : 'border-blue-200 bg-blue-50 text-blue-600',
                                        ]"
                                    >
                                        {{
                                            mapStatusSeleksi(
                                                team.status_seleksi,
                                            )
                                        }}
                                    </span>
                                </td>

                                <!-- Action (Details Modal) -->
                                <td class="px-6 py-4 text-center">
                                    <button
                                        @click="openTeamDetails(team)"
                                        class="inline-flex items-center space-x-1 rounded-lg bg-blue-950 px-3 py-1.5 text-[10px] font-black tracking-wider text-white uppercase shadow-sm transition hover:bg-blue-900"
                                    >
                                        <Info class="h-3.5 w-3.5" />
                                        <span>Detail Anggota</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Custom Details Modal (Centralized glassmorphic look) -->
        <div
            v-if="isDetailModalOpen && selectedTeamDetails"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div
                class="flex min-h-full items-center justify-center p-4 text-center"
            >
                <!-- Backdrop -->
                <div
                    class="fixed inset-0 bg-slate-900/5 backdrop-blur-md transition-opacity"
                    @click="closeTeamDetails"
                ></div>

                <!-- Modal Content Wrapper -->
                <div
                    class="animate-fade-in-up relative z-10 my-8 inline-block w-full max-w-2xl transform overflow-hidden rounded-3xl border border-slate-100 bg-white text-left align-middle shadow-[0_25px_50px_-12px_rgba(0,0,0,0.25)] transition-all"
                >
                    <!-- Header -->
                    <div
                        class="flex items-center justify-between border-b border-slate-100 p-6"
                    >
                        <div class="space-y-1">
                            <h3
                                class="text-lg leading-tight font-black text-slate-900"
                            >
                                Detail Anggota Kelompok
                            </h3>
                            <p
                                class="text-[10px] font-bold tracking-wider text-slate-400 uppercase"
                            >
                                {{ selectedTeamDetails.nama_tim }} -
                                {{ selectedTeamDetails.universitas }}
                            </p>
                        </div>
                        <button
                            @click="closeTeamDetails"
                            class="rounded-lg p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                        >
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="space-y-6 p-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div
                                v-for="(
                                    member, index
                                ) in selectedTeamDetails.sortedMembers"
                                :key="member.id_member"
                                class="space-y-3 rounded-2xl border border-slate-100 bg-slate-50 p-5"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-[9px] font-black tracking-wider text-slate-400 uppercase"
                                    >
                                        {{
                                            member.role === 'ketua'
                                                ? 'Ketua Kelompok'
                                                : `Anggota ${index}`
                                        }}
                                    </span>
                                    <span
                                        class="rounded border px-2 py-0.5 text-[8px] font-black tracking-wider uppercase"
                                        :class="
                                            member.role === 'ketua'
                                                ? 'border-amber-200 bg-amber-50 text-amber-700'
                                                : 'border-slate-200 bg-slate-100 text-slate-600'
                                        "
                                    >
                                        {{ member.role }}
                                    </span>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="space-y-0.5">
                                        <span
                                            class="text-[9px] font-bold tracking-wider text-slate-400 uppercase"
                                            >Nama Lengkap</span
                                        >
                                        <p
                                            class="text-xs font-extrabold text-slate-800"
                                        >
                                            {{ member.nama_peserta }}
                                        </p>
                                    </div>
                                    <div class="space-y-0.5">
                                        <span
                                            class="text-[9px] font-bold tracking-wider text-slate-400 uppercase"
                                            >NIM / Identitas</span
                                        >
                                        <p
                                            class="text-xs font-extrabold text-slate-800"
                                        >
                                            {{ member.nim_peserta }}
                                        </p>
                                    </div>
                                    <div class="space-y-0.5">
                                        <span
                                            class="text-[9px] font-bold tracking-wider text-slate-400 uppercase"
                                            >Jurusan</span
                                        >
                                        <p
                                            class="text-xs font-extrabold text-slate-800"
                                        >
                                            {{ member.jurusan || '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end border-t border-slate-100 p-6">
                        <button
                            @click="closeTeamDetails"
                            class="rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-slate-800"
                        >
                            Tutup Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </CitechDashboardLayout>
</template>

<style scoped>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
