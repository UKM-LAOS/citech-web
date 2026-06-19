<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { 
  Code,
  Users,
  Trophy,
  Sparkles,
  ChevronDown,
  ArrowRight,
  FileText,
  Globe
} from '@lucide/vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    activeTimeline: Object,
    allTimelines: Array,
});

// Navbar Scroll Effect
const isScrolled = ref(false);
const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

// Countdown logic
const days = ref(0);
const hours = ref(0);
const minutes = ref(0);
const seconds = ref(0);
let timer = null;

const targetDate = computed(() => {
    if (props.activeTimeline && props.activeTimeline.tanggal_selesai) {
        return new Date(props.activeTimeline.tanggal_selesai);
    }
    // Fallback date
    return new Date('2026-07-18 23:59:59');
});

const activeStageName = computed(() => {
    if (props.activeTimeline) {
        switch (props.activeTimeline.tahap) {
            case 'pendaftaran_b1': return 'Batch 1 Pendaftaran';
            case 'pendaftaran_b2': return 'Batch 2 Pendaftaran';
            case 'penyisihan': return 'Pengumpulan Berkas';
            case 'final': return 'Babak Final CITECH';
            case 'awarding': return 'Acara Awarding';
            default: return 'CITECH 2026';
        }
    }
    return 'CITECH 2026';
});

const calculateTimeLeft = () => {
    const difference = targetDate.value - new Date();
    if (difference > 0) {
        days.value = Math.floor(difference / (1000 * 60 * 60 * 24));
        hours.value = Math.floor((difference / (1000 * 60 * 60)) % 24);
        minutes.value = Math.floor((difference / 1000 / 60) % 60);
        seconds.value = Math.floor((difference / 1000) % 60);
    } else {
        days.value = 0;
        hours.value = 0;
        minutes.value = 0;
        seconds.value = 0;
        clearInterval(timer);
    }
};

// Section Reveal Observer
const revealedSections = ref({
    tentang: false,
    countdown: false,
    timeline: false,
    sponsor: false,
    faq: false,
});

onMounted(() => {
    calculateTimeLeft();
    timer = setInterval(calculateTimeLeft, 1000);
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // initial check

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const id = entry.target.id;
                if (id && id in revealedSections.value) {
                    revealedSections.value[id] = true;
                }
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('section[id]').forEach((sec) => {
        observer.observe(sec);
    });
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
    window.removeEventListener('scroll', handleScroll);
});

// FAQ Accordion
const activeFaq = ref(null);
const toggleFaq = (index) => {
    activeFaq.value = activeFaq.value === index ? null : index;
};

const faqs = [
    {
        q: "Apakah kompetisi ini berbayar?",
        a: "Ya, pendaftaran dikenakan biaya pendaftaran sesuai dengan batch pendaftaran kategori lomba Web Design. Informasi lengkap nominal biaya dapat dilihat pada panduan pendaftaran."
    },
    {
        q: "Berapa orang dalam satu tim?",
        a: "Kategori perlombaan Web Design ini dapat diikuti secara individu maupun tim (maksimal 3 orang per tim). Anggota tim dapat dikonfigurasi langsung di dashboard setelah membuat tim."
    },
    {
        q: "Apakah perlombaan dilaksanakan online atau offline?",
        a: "Babak penyisihan dan pengumpulan berkas dilakukan secara online melalui website ini. Sedangkan babak final dan awarding akan diselenggarakan secara offline di Universitas Jember."
    },
    {
        q: "Dimana saya dapat melihat informasi terbaru?",
        a: "Informasi terbaru akan selalu diperbarui di dashboard peserta website ini, serta dipublikasikan melalui akun Instagram resmi kami @citech_unej."
    }
];

// Timeline static fallback
const timelineItemsFallback = [
    {
        tahap: 'Batch 1 Pendaftaran',
        desc: 'Pendaftaran Seluruh Peserta Batch 1',
        date: '27 Juni - 18 Juli 2026',
        alignLeft: false,
    },
    {
        tahap: 'Batch 2 Pendaftaran',
        desc: 'Pendaftaran Seluruh Peserta Batch 2',
        date: '19 Juli - 1 Agustus 2026',
        alignLeft: true,
    },
    {
        tahap: 'Pengumpulan Berkas',
        desc: 'Pengumpulan Berkas Babak Penyisihan',
        date: '27 Juni - 1 Agustus 2026',
        alignLeft: false,
    },
    {
        tahap: 'Pengumuman Lolos',
        desc: 'Pengumuman Lolos Babak Penyisihan',
        date: '10 Agustus 2026',
        alignLeft: true,
    },
    {
        tahap: 'Technical Meeting Finalis',
        desc: 'Technical Meeting Finalis',
        date: '11 Agustus 2026',
        alignLeft: false,
    },
    {
        tahap: 'Pengumpulan Berkas Final',
        desc: 'Pengumpulan Berkas Babak Final',
        date: '19 Agustus - 21 Agustus 2026',
        alignLeft: true,
    },
    {
        tahap: 'Final & Awarding',
        desc: 'Final & Awarding',
        date: '23 Agustus 2026',
        alignLeft: false,
    },
];

// Dynamic timeline items mapped from Database
const formattedTimelineItems = computed(() => {
    if (!props.allTimelines || props.allTimelines.length === 0) {
        return timelineItemsFallback.map(item => ({
            ...item,
            isActive: props.activeTimeline?.tahap === item.tahap
        }));
    }
    
    const formatRange = (startStr, endStr) => {
        if (!startStr || !endStr) return '';
        const start = new Date(startStr);
        const end = new Date(endStr);
        const months = ['Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        // Since we know the database dates are in June-August:
        const getMonthName = (date) => {
            const m = date.getMonth();
            const monthsFull = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            return monthsFull[m];
        };

        if (start.getFullYear() === end.getFullYear()) {
            if (start.getMonth() === end.getMonth()) {
                return `${start.getDate()} - ${end.getDate()} ${getMonthName(start)} ${start.getFullYear()}`;
            } else {
                return `${start.getDate()} ${getMonthName(start)} - ${end.getDate()} ${getMonthName(end)} ${start.getFullYear()}`;
            }
        }
        return `${start.getDate()} ${getMonthName(start)} ${start.getFullYear()} - ${end.getDate()} ${getMonthName(end)} ${end.getFullYear()}`;
    };

    const b1 = props.allTimelines.find(t => t.tahap === 'pendaftaran_b1');
    const b2 = props.allTimelines.find(t => t.tahap === 'pendaftaran_b2');
    const penyisihan = props.allTimelines.find(t => t.tahap === 'penyisihan');
    const final = props.allTimelines.find(t => t.tahap === 'final');
    const awarding = props.allTimelines.find(t => t.tahap === 'awarding');

    const items = [];
    if (b1) {
        items.push({
            tahap: 'Batch 1 Pendaftaran',
            desc: 'Pendaftaran Seluruh Peserta Batch 1',
            date: formatRange(b1.tanggal_mulai, b1.tanggal_selesai),
            alignLeft: false,
            isActive: props.activeTimeline?.tahap === 'pendaftaran_b1'
        });
    }
    if (b2) {
        items.push({
            tahap: 'Batch 2 Pendaftaran',
            desc: 'Pendaftaran Seluruh Peserta Batch 2',
            date: formatRange(b2.tanggal_mulai, b2.tanggal_selesai),
            alignLeft: true,
            isActive: props.activeTimeline?.tahap === 'pendaftaran_b2'
        });
    }
    if (penyisihan) {
        items.push({
            tahap: 'Pengumpulan Berkas (Penyisihan)',
            desc: 'Pengumpulan Berkas Babak Penyisihan',
            date: formatRange(penyisihan.tanggal_mulai, penyisihan.tanggal_selesai),
            alignLeft: false,
            isActive: props.activeTimeline?.tahap === 'penyisihan'
        });
    }
    
    // Intermediate chronological dates relative to Final
    let pengumumanDate = '10 Agustus 2026';
    let tmDate = '11 Agustus 2026';
    if (final && final.tanggal_mulai) {
        const finalStart = new Date(final.tanggal_mulai);
        const dMinus1 = new Date(finalStart);
        dMinus1.setDate(dMinus1.getDate() - 1);
        const monthsFull = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        pengumumanDate = `${dMinus1.getDate()} ${monthsFull[dMinus1.getMonth()]} ${dMinus1.getFullYear()}`;
        tmDate = `${finalStart.getDate()} ${monthsFull[finalStart.getMonth()]} ${finalStart.getFullYear()}`;
    }

    items.push({
        tahap: 'Pengumuman Lolos Penyisihan',
        desc: 'Pengumuman Peserta Lolos ke Babak Final',
        date: pengumumanDate,
        alignLeft: true,
        isActive: false
    });
    
    items.push({
        tahap: 'Technical Meeting Finalis',
        desc: 'Pemberian arahan dan panduan untuk Babak Final',
        date: tmDate,
        alignLeft: false,
        isActive: false
    });

    if (final) {
        items.push({
            tahap: 'Pengumpulan Berkas Final',
            desc: 'Pengumpulan Berkas untuk Penjurian Final',
            date: formatRange(final.tanggal_mulai, final.tanggal_selesai),
            alignLeft: true,
            isActive: props.activeTimeline?.tahap === 'final'
        });
    }

    if (awarding) {
        items.push({
            tahap: 'Final & Awarding CITECH',
            desc: 'Presentasi Finalis dan Acara Penganugerahan Pemenang',
            date: formatRange(awarding.tanggal_mulai, awarding.tanggal_selesai),
            alignLeft: false,
            isActive: props.activeTimeline?.tahap === 'awarding'
        });
    }

    return items;
});
</script>

<template>
    <Head title="CITECH 2026 - Carnival Technology UNEJ" />
    
    <div class="relative min-h-screen bg-slate-50/20 text-slate-700 font-sans overflow-x-hidden animate-page-fade">
        
        <!-- Floating background blobs -->
        <div class="absolute top-[10%] left-[5%] w-[400px] h-[400px] rounded-full bg-blue-300/15 blur-[100px] animate-float-slow pointer-events-none z-0"></div>
        <div class="absolute top-[40%] right-[5%] w-[450px] h-[450px] rounded-full bg-purple-300/15 blur-[120px] animate-float-delayed pointer-events-none z-0"></div>
        <div class="absolute bottom-[20%] left-[10%] w-[350px] h-[350px] rounded-full bg-orange-200/10 blur-[90px] pointer-events-none z-0"></div>

        <!-- Sticky Header/Navbar (Transitions dynamically based on scroll) -->
        <header 
            class="fixed top-0 left-0 right-0 z-50 w-full transition-all duration-[1000ms] ease-[cubic-bezier(0.16,1,0.3,1)] animate-navbar"
            :class="[
                isScrolled 
                    ? 'bg-white/90 backdrop-blur-md border-b border-slate-100 shadow-md py-4' 
                    : 'bg-transparent py-6 border-b border-transparent'
            ]"
        >
            <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3 transition-all duration-300 hover:scale-[1.02] cursor-pointer animate-nav-item">
                    <img src="/assets/logo-citech.png" alt="Logo CITECH" class="h-10 w-auto object-contain" />
                    <span 
                        class="text-2xl font-extrabold tracking-wider text-blue-900"
                    >
                        CITECH
                    </span>
                </div>

                <!-- Navigation Links with smooth transition & hover underline animation -->
                <nav class="hidden md:flex items-center space-x-10 text-sm font-semibold">
                    <a href="#tentang" class="relative py-2 transition-all duration-300 group text-slate-700 hover:text-blue-900 animate-nav-item delay-100">
                        Tentang
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#1e4d8c] transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#timeline" class="relative py-2 transition-all duration-300 group text-slate-700 hover:text-blue-900 animate-nav-item delay-200">
                        Timeline
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#1e4d8c] transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#sponsor" class="relative py-2 transition-all duration-300 group text-slate-700 hover:text-blue-900 animate-nav-item delay-300">
                        Event
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#1e4d8c] transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#faq" class="relative py-2 transition-all duration-300 group text-slate-700 hover:text-blue-900 animate-nav-item delay-400">
                        FAQ
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#1e4d8c] transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </nav>

                <!-- Auth / Call To Action Button -->
                <div class="animate-nav-item delay-500">
                    <template v-if="$page.props.auth.user">
                        <Link 
                            :href="route('dashboard')" 
                            class="bg-[#3769a6] hover:bg-[#2b5487] hover:scale-[1.05] hover:shadow-lg hover:shadow-blue-500/25 text-white font-bold text-xs px-6 py-2.5 rounded-lg transition-all duration-300 active:scale-95 inline-block"
                        >
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link 
                            :href="route('login')" 
                            class="bg-[#3769a6] hover:bg-[#2b5487] hover:scale-[1.05] hover:shadow-lg hover:shadow-blue-500/25 text-white font-bold text-xs px-6 py-2.5 rounded-lg transition-all duration-300 active:scale-95 inline-block"
                        >
                            Daftar
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero Section (Full viewport height, centered content, premium layout) -->
        <section class="relative min-h-screen flex flex-col justify-center items-center px-6 pt-32 pb-24 text-center z-10 bg-gradient-to-b from-[#f4f7fe]/70 via-white to-white overflow-hidden">
            <!-- Tech Dotted Grid Overlay -->
            <div class="absolute inset-0 bg-[radial-gradient(#cbd5e1_1.2px,transparent_1.2px)] [background-size:24px_24px] opacity-40 [mask-image:radial-gradient(ellipse_60%_60%_at_50%_40%,#000_60%,transparent_100%)] pointer-events-none z-0"></div>

            <!-- Floating Code Syntax Tags (Styled as glassmorphic editor stickers) -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none select-none z-0">
                <div class="absolute hidden lg:block bg-blue-50/60 backdrop-blur-[2px] border border-blue-100/80 px-3.5 py-2 rounded-2xl text-blue-900/70 font-mono text-sm top-[20%] left-[10%] animate-float-slow">&lt;div class="citech"&gt;</div>
                <div class="absolute hidden lg:block bg-slate-50/60 backdrop-blur-[2px] border border-slate-200/80 px-3.5 py-1.5 rounded-xl text-slate-700/80 font-mono text-xs top-[64%] left-[16%] animate-float-delayed">const year = 2026;</div>
                <div class="absolute hidden lg:block bg-amber-50/60 backdrop-blur-[2px] border border-amber-100/80 px-3.5 py-2 rounded-2xl text-amber-800/80 font-mono text-xs top-[28%] right-[12%] animate-float-slow">color: "#eab308"</div>
                <div class="absolute hidden lg:block bg-blue-50/60 backdrop-blur-[2px] border border-blue-100/80 px-3.5 py-2 rounded-2xl text-blue-900/70 font-mono text-sm top-[58%] right-[10%] animate-float-delayed">design: "premium"</div>
                <div class="absolute hidden lg:block bg-purple-50/60 backdrop-blur-[2px] border border-purple-100/80 px-3.5 py-2 rounded-2xl text-purple-900/70 font-mono text-xs top-[42%] left-[6%] animate-float-delayed">&lt;template&gt;</div>
                <div class="absolute hidden lg:block bg-purple-50/60 backdrop-blur-[2px] border border-purple-100/80 px-3.5 py-2 rounded-2xl text-purple-900/70 font-mono text-xs top-[47%] right-[6%] animate-float-slow">&lt;/template&gt;</div>
            </div>

            <!-- Two Pills in Header -->
            <div class="flex flex-wrap items-center justify-center gap-3 mb-8 animate-hero-pills">
                <!-- Pill 1 -->
                <div class="inline-flex items-center space-x-2 bg-purple-50 border border-purple-100 px-4 py-1.5 rounded-full text-xs font-semibold text-purple-700 shadow-sm transition hover:scale-105 duration-300">
                    <Globe class="w-3.5 h-3.5 animate-spin-slow" />
                    <span>National Competition</span>
                </div>
                <!-- Pill 2 -->
                <div class="inline-flex items-center bg-slate-50 border border-slate-200/80 px-4 py-1.5 rounded-full text-xs font-semibold text-slate-600 shadow-sm transition hover:scale-105 duration-300">
                    <span>UKM LAOS UNEJ</span>
                </div>
            </div>

            <!-- Main Title with premium scaling/weight and gradient backdrop aura -->
            <div class="relative max-w-4xl mx-auto mb-6 animate-hero-title z-10">
                <!-- Soft Glow Backdrop behind the title -->
                <div class="absolute -inset-4 bg-gradient-to-r from-blue-300/20 via-purple-300/20 to-amber-300/20 blur-[60px] rounded-full -z-10 animate-float-slow"></div>
                <h1 class="text-4xl sm:text-7xl font-extrabold tracking-tight text-[#0c2448] leading-[1.08] cursor-default text-shadow-blue">
                    Carnival Technology <br/>
                    <span class="text-[#eab308] relative inline-block transition-transform hover:scale-105 duration-300 text-shadow-gold">UNEJ 2026</span>
                </h1>
            </div>

            <!-- Description -->
            <p class="text-slate-500 max-w-2xl mx-auto leading-relaxed text-sm sm:text-base mb-12 animate-hero-desc z-10">
                Kompetisi Web Design tingkat nasional yang mempertemukan talenta digital muda untuk berkreasi, berinovasi, dan berkontribusi dalam perkembangan teknologi Indonesia.
            </p>

            <!-- Interactive Buttons with hover translate & scale -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full max-w-md mb-20 animate-hero-buttons z-10">
                <Link 
                    :href="route('register')" 
                    class="w-full sm:w-auto bg-[#1e4d8c] hover:bg-[#153a6b] hover:scale-[1.03] active:scale-95 hover:shadow-lg hover:shadow-blue-500/20 text-white font-bold text-sm px-10 py-4 rounded-xl transition-all duration-300 flex items-center justify-center"
                >
                    <span>Daftar Sekarang</span>
                </Link>
                <a 
                    href="#" 
                    class="w-full sm:w-auto bg-white border border-slate-200 hover:bg-slate-50 hover:scale-[1.03] active:scale-95 text-slate-700 font-bold text-sm px-10 py-4 rounded-xl transition-all duration-300 flex items-center justify-center space-x-2 shadow-sm"
                >
                    <FileText class="w-4 h-4 text-slate-400" />
                    <span>Guidebook</span>
                </a>
            </div>

            <!-- Double down arrow anchor pointing to about section -->
            <a 
                href="#tentang" 
                class="absolute bottom-10 left-1/2 -translate-x-1/2 text-slate-400 hover:text-blue-900 transition-colors duration-300 flex flex-col items-center space-y-1 group animate-hero-indicator z-10"
            >
                <span class="text-xs font-bold tracking-widest uppercase opacity-70 group-hover:opacity-100 transition-opacity duration-300">Selengkapnya</span>
                <ChevronDown class="w-5 h-5 animate-bounce" />
            </a>
        </section>

        <!-- Tentang Citech Section (Seamless gradient from bottom of hero) -->
        <section 
            id="tentang" 
            class="relative py-32 bg-gradient-to-b from-white via-[#f4f7fe] to-white overflow-hidden"
        >
            <!-- Background Glow Blobs -->
            <div class="absolute top-[10%] right-[-5%] w-[350px] h-[350px] rounded-full bg-blue-300/15 blur-[100px] pointer-events-none z-0"></div>
            <div class="absolute bottom-[5%] left-[-5%] w-[350px] h-[350px] rounded-full bg-purple-300/10 blur-[100px] pointer-events-none z-0"></div>

            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="text-center mb-20 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform" :class="[revealedSections.tentang ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest">Tentang Event</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-blue-900 mt-2">Tentang Citech</h2>
                </div>

                <!-- Main Glass Card with hover lift and glow -->
                <div class="relative max-w-4xl mx-auto mb-20 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform delay-[200ms]" :class="[revealedSections.tentang ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                    <!-- Soft gradient shadow behind the card -->
                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-300/20 via-purple-300/15 to-blue-200/20 blur-[50px] rounded-[40px] -z-10 pointer-events-none opacity-60"></div>
                    <div class="bg-white/90 backdrop-blur-sm border border-slate-200/50 rounded-[32px] p-8 md:p-14 shadow-[0_15px_40px_-20px_rgba(0,0,0,0.06)] hover:shadow-[0_30px_70px_rgba(30,77,140,0.08)] hover:scale-[1.01] transition-all duration-500">
                        <h3 class="text-xl md:text-2xl font-bold text-slate-900 mb-6 text-center leading-snug">
                            Wadah talenta digital muda di Indonesia.
                        </h3>
                        <div class="space-y-6 text-slate-500 leading-relaxed text-sm md:text-base">
                            <p>
                                CITECH (Carnival Technology) UNEJ merupakan kompetisi Web Design tingkat nasional yang diselenggarakan oleh UKM LAOS Universitas Jember sebagai wadah pengembangan kreativitas dan kemampuan mahasiswa jenjang D3, D4, dan S1 di bidang teknologi digital.
                            </p>
                            <p>
                                Dengan target peserta dari kalangan mahasiswa dan komunitas teknologi, kegiatan ini juga menjadi peluang strategis bagi sponsor untuk meningkatkan brand awareness serta menunjukkan dukungan terhadap pengembangan talenta digital muda.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 4 Feature Cards (Interactive lift and glow) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 max-w-6xl mx-auto">
                    <!-- Card 1 -->
                    <div class="bg-white border border-slate-100 rounded-2xl p-7 shadow-sm hover:shadow-xl hover:border-blue-400/40 hover:-translate-y-2 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform delay-[300ms] flex flex-col items-start group" :class="[revealedSections.tentang ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 mb-5 group-hover:scale-110 group-hover:bg-orange-100 transition-all duration-300">
                            <Code class="w-5 h-5" />
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm mb-2 group-hover:text-blue-900 transition-colors">Web Design</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">Kompetisi desain web tingkat nasional.</p>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white border border-slate-100 rounded-2xl p-7 shadow-sm hover:shadow-xl hover:border-blue-400/40 hover:-translate-y-2 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform delay-[400ms] flex flex-col items-start group" :class="[revealedSections.tentang ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 mb-5 group-hover:scale-110 group-hover:bg-orange-100 transition-all duration-300">
                            <Users class="w-5 h-5" />
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm mb-2 group-hover:text-blue-900 transition-colors">Mahasiswa D3/D4/S1</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">Terbuka untuk seluruh mahasiswa Indonesia.</p>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white border border-slate-100 rounded-2xl p-7 shadow-sm hover:shadow-xl hover:border-blue-400/40 hover:-translate-y-2 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform delay-[500ms] flex flex-col items-start group" :class="[revealedSections.tentang ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 mb-5 group-hover:scale-110 group-hover:bg-orange-100 transition-all duration-300">
                            <Trophy class="w-5 h-5" />
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm mb-2 group-hover:text-blue-900 transition-colors">Hadiah Menarik</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">Total hadiah jutaan rupiah & sertifikat.</p>
                    </div>

                    <!-- Card 4 -->
                    <div class="bg-white border border-slate-100 rounded-2xl p-7 shadow-sm hover:shadow-xl hover:border-blue-400/40 hover:-translate-y-2 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform delay-[600ms] flex flex-col items-start group" :class="[revealedSections.tentang ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 mb-5 group-hover:scale-110 group-hover:bg-orange-100 transition-all duration-300">
                            <Sparkles class="w-5 h-5" />
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm mb-2 group-hover:text-blue-900 transition-colors">Pengembangan Talenta</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">Wadah pengembangan kreativitas digital.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Countdown Section (Dynamic stage heading) -->
        <section 
            id="countdown" 
            class="py-24 text-center bg-gradient-to-b from-white via-slate-50/50 to-white"
        >
            <div class="transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform" :class="[revealedSections.countdown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Hitung Mundur</span>
                <h2 class="text-3xl font-extrabold text-[#0c2448] mt-2 mb-12">Menuju {{ activeStageName }}</h2>
            </div>

            <!-- Countdown Cards -->
            <div class="flex items-center justify-center space-x-3 sm:space-x-6 max-w-4xl mx-auto px-6">
                <!-- Days -->
                <div class="flex flex-col bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-9 w-24 sm:w-36 shadow-sm hover:shadow-md hover:border-amber-400/50 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform delay-[200ms] group" :class="[revealedSections.countdown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                    <span class="text-4xl sm:text-6xl font-extrabold text-amber-500 tracking-tight mb-2 group-hover:scale-105 transition-transform">{{ String(days).padStart(2, '0') }}</span>
                    <span class="text-xs font-bold text-slate-400 uppercase">Hari</span>
                </div>
                <!-- Hours -->
                <div class="flex flex-col bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-9 w-24 sm:w-36 shadow-sm hover:shadow-md hover:border-amber-400/50 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform delay-[300ms] group" :class="[revealedSections.countdown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                    <span class="text-4xl sm:text-6xl font-extrabold text-amber-500 tracking-tight mb-2 group-hover:scale-105 transition-transform">{{ String(hours).padStart(2, '0') }}</span>
                    <span class="text-xs font-bold text-slate-400 uppercase">Jam</span>
                </div>
                <!-- Minutes -->
                <div class="flex flex-col bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-9 w-24 sm:w-36 shadow-sm hover:shadow-md hover:border-amber-400/50 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform delay-[400ms] group" :class="[revealedSections.countdown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                    <span class="text-4xl sm:text-6xl font-extrabold text-amber-500 tracking-tight mb-2 group-hover:scale-105 transition-transform">{{ String(minutes).padStart(2, '0') }}</span>
                    <span class="text-xs font-bold text-slate-400 uppercase">Menit</span>
                </div>
                <!-- Seconds -->
                <div class="flex flex-col bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-9 w-24 sm:w-36 shadow-sm hover:shadow-md hover:border-amber-400/50 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform delay-[500ms] group" :class="[revealedSections.countdown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                    <span class="text-4xl sm:text-6xl font-extrabold text-amber-500 tracking-tight mb-2 group-hover:scale-105 transition-transform">{{ String(seconds).padStart(2, '0') }}</span>
                    <span class="text-xs font-bold text-slate-400 uppercase">Detik</span>
                </div>
            </div>
        </section>

        <!-- Timeline Section (Redesigned: Larger, interactive, and synced with DB) -->
        <section 
            id="timeline" 
            class="py-32 bg-gradient-to-b from-white via-[#f8faff] to-white border-t border-slate-100"
        >
            <div class="max-w-5xl mx-auto px-6">
                <div class="text-center mb-24 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform" :class="[revealedSections.timeline ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest">Rangkaian Acara</span>
                    <h2 class="text-4xl font-extrabold text-[#0c2448] mt-2">Timeline CITECH</h2>
                    <p class="text-slate-400 text-sm mt-3">Jadwal kegiatan perlombaan resmi disinkronkan dari database</p>
                </div>

                <!-- Vertical Alternating Timeline -->
                <div class="relative">
                    <!-- Central line -->
                    <div class="absolute left-1/2 transform -translate-x-1/2 h-full w-[4px] bg-[#1e4d8c]/15"></div>

                    <!-- Timeline Loop -->
                    <div class="space-y-16 relative z-10">
                        <div 
                            v-for="(item, index) in formattedTimelineItems" 
                            :key="index"
                            class="flex flex-col md:flex-row items-center md:justify-between relative transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform"
                            :class="[revealedSections.timeline ? 'opacity-100 translate-x-0' : (item.alignLeft ? 'opacity-0 -translate-x-16' : 'opacity-0 translate-x-16'), {'md:flex-row-reverse': item.alignLeft}]"
                            :style="{ transitionDelay: revealedSections.timeline ? `${200 + index * 150}ms` : '0ms' }"
                        >
                            <!-- Line dot connector with pulse ring on active -->
                            <div 
                                class="absolute left-1/2 transform -translate-x-1/2 top-6 w-6 h-6 rounded-full border-4 bg-white flex items-center justify-center transition-all duration-500 z-20"
                                :class="[
                                    item.isActive 
                                        ? 'border-amber-500 scale-125 shadow-md shadow-amber-500/35 ring-4 ring-amber-500/25' 
                                        : 'border-[#1e4d8c]'
                                ]"
                            >
                                <span v-if="item.isActive" class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <span v-else class="w-2 h-2 rounded-full bg-[#1e4d8c]/80"></span>
                            </div>

                            <!-- Date Badge Column (Pill with solid highlight) -->
                            <div class="w-full md:w-1/2 flex justify-center px-12" :class="[item.alignLeft ? 'md:justify-start' : 'md:justify-end']">
                                <div 
                                    class="font-bold text-sm px-8 py-4 rounded-2xl transition duration-300 shadow-md tracking-wide text-center"
                                    :class="[
                                        item.isActive
                                            ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/20 hover:scale-105'
                                            : 'bg-[#1e4d8c] hover:bg-[#153a6b] text-white shadow-blue-900/10 hover:scale-105'
                                    ]"
                                >
                                    {{ item.date }}
                                </div>
                            </div>

                            <!-- Content details panel -->
                            <div class="w-full md:w-1/2 px-12 mt-6 md:mt-0 text-center" :class="[item.alignLeft ? 'md:text-right' : 'md:text-left']">
                                <div 
                                    class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:border-blue-300/60 hover:-translate-y-1 transition-all duration-300"
                                    :class="[
                                        item.isActive 
                                            ? 'ring-2 ring-amber-400/50 border-amber-300 shadow-md bg-amber-500/[0.01]' 
                                            : ''
                                    ]"
                                >
                                    <!-- Badge status -->
                                    <div v-if="item.isActive" class="inline-flex items-center space-x-1.5 bg-amber-100 px-3 py-0.5 rounded-full text-[10px] font-bold text-amber-800 mb-2.5 uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-ping"></span>
                                        <span>Berlangsung</span>
                                    </div>
                                    <h4 class="font-extrabold text-slate-900 text-xl mb-2">{{ item.tahap }}</h4>
                                    <p class="text-sm text-slate-500 leading-relaxed">{{ item.desc }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sponsor Section (Seamless Infinite Horizontal Marquee moving Right-to-Left) -->
        <section 
            id="sponsor" 
            class="py-28 bg-[#fdfdfd] border-t border-slate-100 overflow-hidden"
        >
            <div class="max-w-7xl mx-auto text-center px-6 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform" :class="[revealedSections.sponsor ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sponsored By</span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-[#0c2448] mt-2 mb-16">Didukung oleh brand & mitra teknologi terbaik</h2>
            </div>
                
            <!-- Infinite Horizontal Scrolling Wrapper -->
            <div class="relative w-full overflow-hidden mask-gradient py-6 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform delay-[300ms]" :class="[revealedSections.sponsor ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                <div class="flex whitespace-nowrap animate-marquee">
                    <!-- Group of logos (1st half) -->
                    <div class="flex items-center space-x-24 shrink-0 pr-24">
                        <span class="text-2xl font-black tracking-widest text-slate-300 hover:text-[#1e4d8c] transition duration-300 select-none cursor-default">TECHCORP</span>
                        <span class="text-2xl font-black tracking-widest text-slate-300 hover:text-[#1e4d8c] transition duration-300 select-none cursor-default">NEXALABS</span>
                        <span class="text-2xl font-black tracking-widest text-slate-300 hover:text-[#1e4d8c] transition duration-300 select-none cursor-default">PIXELHUB</span>
                        <span class="text-2xl font-black tracking-widest text-slate-300 hover:text-[#1e4d8c] transition duration-300 select-none cursor-default">CLOUDIFY</span>
                        <span class="text-2xl font-black tracking-widest text-slate-300 hover:text-[#1e4d8c] transition duration-300 select-none cursor-default">DEVFORGE</span>
                        <span class="text-2xl font-black tracking-widest text-slate-300 hover:text-[#1e4d8c] transition duration-300 select-none cursor-default">BRANDIO</span>
                    </div>
                    <!-- Group of logos (2nd half for seamless transition) -->
                    <div class="flex items-center space-x-24 shrink-0 pr-24" aria-hidden="true">
                        <span class="text-2xl font-black tracking-widest text-slate-300 hover:text-[#1e4d8c] transition duration-300 select-none cursor-default">TECHCORP</span>
                        <span class="text-2xl font-black tracking-widest text-slate-300 hover:text-[#1e4d8c] transition duration-300 select-none cursor-default">NEXALABS</span>
                        <span class="text-2xl font-black tracking-widest text-slate-300 hover:text-[#1e4d8c] transition duration-300 select-none cursor-default">PIXELHUB</span>
                        <span class="text-2xl font-black tracking-widest text-slate-300 hover:text-[#1e4d8c] transition duration-300 select-none cursor-default">CLOUDIFY</span>
                        <span class="text-2xl font-black tracking-widest text-slate-300 hover:text-[#1e4d8c] transition duration-300 select-none cursor-default">DEVFORGE</span>
                        <span class="text-2xl font-black tracking-widest text-slate-300 hover:text-[#1e4d8c] transition duration-300 select-none cursor-default">BRANDIO</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section 
            id="faq" 
            class="py-28 border-t border-slate-100 bg-[#f9fbff]/40"
        >
            <div class="max-w-3xl mx-auto px-6">
                <div class="text-center mb-16 transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform" :class="[revealedSections.faq ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest">FAQ</span>
                    <h2 class="text-3xl font-extrabold text-[#0c2448] mt-2">Pertanyaan yang sering diajukan</h2>
                </div>

                <!-- FAQ Accordion List with hover and smooth layout changes -->
                <div class="space-y-4">
                    <div 
                        v-for="(faq, index) in faqs" 
                        :key="index"
                        class="border border-slate-200 rounded-2xl bg-white overflow-hidden transition-all duration-[1500ms] ease-[cubic-bezier(0.16,1,0.3,1)] transform"
                        :class="[revealedSections.faq ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16', activeFaq === index ? 'border-blue-300/80 shadow-[0_5px_15px_-10px_rgba(0,0,0,0.05)] bg-[#f4f7fe]/10' : '']"
                        :style="{ transitionDelay: revealedSections.faq ? `${200 + index * 120}ms` : '0ms' }"
                    >
                        <button 
                            @click="toggleFaq(index)"
                            class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none hover:bg-slate-50/50 transition duration-300"
                        >
                            <span class="font-extrabold text-slate-800 text-sm md:text-base">{{ faq.q }}</span>
                            <ChevronDown 
                                class="w-5 h-5 text-slate-400 transition-transform duration-300"
                                :class="{'rotate-180 text-blue-500': activeFaq === index}"
                            />
                        </button>
                        <div 
                            v-show="activeFaq === index" 
                            class="px-6 pb-5 text-slate-500 text-xs md:text-sm leading-relaxed border-t border-slate-100/80 pt-3"
                        >
                            {{ faq.a }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer Section -->
        <footer class="bg-[#0b0f19] text-slate-400 py-20 relative overflow-hidden">
            <!-- Subtle glow in footer -->
            <div class="absolute -right-32 -bottom-32 w-80 h-80 rounded-full bg-blue-500/5 blur-3xl pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 border-b border-slate-800/80 pb-16 mb-8">
                    <!-- Info Column -->
                    <div class="md:col-span-2 space-y-6">
                        <div class="flex items-center space-x-3 group cursor-pointer transition-all duration-300">
                            <img src="/assets/logo-citech.png" alt="Logo CITECH" class="h-10 w-auto object-contain group-hover:scale-105 transition-transform duration-300" />
                            <span class="text-white text-lg font-extrabold tracking-tight">CITECH UNEJ</span>
                        </div>
                        <p class="text-xs leading-relaxed max-w-sm text-slate-400">
                            Carnival Technology — kompetisi Web Design nasional yang diselenggarakan UKM LAOS Universitas Jember.
                        </p>
                        <div class="flex space-x-3.5 pt-2">
                            <!-- Social Media Links (Simulated inline SVGs for stability) -->
                            <a href="#" class="w-9 h-9 rounded-full border border-slate-800 flex items-center justify-center hover:border-slate-600 hover:scale-110 hover:bg-slate-800 transition-all duration-300 text-slate-400 hover:text-white">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                            </a>
                            <a href="#" class="w-9 h-9 rounded-full border border-slate-800 flex items-center justify-center hover:border-slate-600 hover:scale-110 hover:bg-slate-800 transition-all duration-300 text-slate-400 hover:text-white">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                            </a>
                            <a href="#" class="w-9 h-9 rounded-full border border-slate-800 flex items-center justify-center hover:border-slate-600 hover:scale-110 hover:bg-slate-800 transition-all duration-300 text-slate-400 hover:text-white">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Nav Column -->
                    <div>
                        <h5 class="text-white text-xs font-bold uppercase tracking-wider mb-5">Navigasi</h5>
                        <ul class="space-y-3.5 text-xs">
                            <li><a href="#tentang" class="hover:text-white hover:underline transition">Tentang</a></li>
                            <li><a href="#timeline" class="hover:text-white hover:underline transition">Timeline</a></li>
                            <li><a href="#sponsor" class="hover:text-white hover:underline transition">Event</a></li>
                            <li><a href="#faq" class="hover:text-white hover:underline transition">FAQ</a></li>
                        </ul>
                    </div>

                    <!-- Contact Column -->
                    <div>
                        <h5 class="text-white text-xs font-bold uppercase tracking-wider mb-5">Kontak</h5>
                        <ul class="space-y-3.5 text-xs">
                            <li>UKM LAOS UNEJ</li>
                            <li>Universitas Jember</li>
                            <li class="text-blue-400 hover:underline"><a href="mailto:citech@unej.ac.id">citech@unej.ac.id</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom Footer -->
                <div class="flex flex-col md:flex-row items-center justify-between text-[11px] text-slate-600">
                    <p>&copy; 2026 CITECH UNEJ. All rights reserved.</p>
                    <p class="mt-2 md:mt-0">Made with care by UKM LAOS Universitas Jember.</p>
                </div>
            </div>
        </footer>

    </div>
</template>

<style scoped>
@keyframes pageFade {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-page-fade {
    animation: pageFade 1.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes slideDown {
    from { transform: translateY(-100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(24px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-navbar {
    animation: slideDown 1.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.animate-nav-item {
    animation: fadeInUp 1.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    opacity: 0;
}
.animate-hero-pills {
    animation: fadeInUp 1.5s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards;
    opacity: 0;
}
.animate-hero-title {
    animation: fadeInUp 1.5s cubic-bezier(0.16, 1, 0.3, 1) 0.3s forwards;
    opacity: 0;
}
.animate-hero-desc {
    animation: fadeInUp 1.5s cubic-bezier(0.16, 1, 0.3, 1) 0.45s forwards;
    opacity: 0;
}
.animate-hero-buttons {
    animation: fadeInUp 1.5s cubic-bezier(0.16, 1, 0.3, 1) 0.6s forwards;
    opacity: 0;
}
.animate-hero-indicator {
    animation: fadeIn 2s ease-out 1.4s forwards;
    opacity: 0;
}
.animate-spin-slow {
    animation: spin 8s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.delay-100 {
    animation-delay: 0.1s;
}
.delay-200 {
    animation-delay: 0.2s;
}
.delay-300 {
    animation-delay: 0.3s;
}
.delay-400 {
    animation-delay: 0.4s;
}
.delay-500 {
    animation-delay: 0.5s;
}
.text-shadow-blue {
    text-shadow: 0 4px 20px rgba(12, 36, 72, 0.2), 0 8px 32px rgba(12, 36, 72, 0.1), 0 1px 2px rgba(12, 36, 72, 0.06);
}
.text-shadow-gold {
    text-shadow: 0 4px 20px rgba(234, 179, 8, 0.35), 0 8px 32px rgba(234, 179, 8, 0.18), 0 1px 2px rgba(234, 179, 8, 0.12);
}
</style>
