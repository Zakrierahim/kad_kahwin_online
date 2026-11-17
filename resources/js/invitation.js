const countdown = document.querySelector('[data-countdown]');

if (countdown) {
    const target = new Date(countdown.getAttribute('data-countdown') || '').getTime();
    const units = {
        days: countdown.querySelector('[data-unit="days"]'),
        hours: countdown.querySelector('[data-unit="hours"]'),
        minutes: countdown.querySelector('[data-unit="minutes"]'),
        seconds: countdown.querySelector('[data-unit="seconds"]'),
    };
    const expired = document.querySelector('[data-countdown-expired]');

    const render = () => {
        const remaining = Math.max(0, target - Date.now());
        const totalSeconds = Math.floor(remaining / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        if (units.days) {
            units.days.textContent = String(days);
        }

        if (units.hours) {
            units.hours.textContent = String(hours).padStart(2, '0');
        }

        if (units.minutes) {
            units.minutes.textContent = String(minutes).padStart(2, '0');
        }

        if (units.seconds) {
            units.seconds.textContent = String(seconds).padStart(2, '0');
        }

        if (remaining <= 0) {
            window.clearInterval(timer);
            countdown.hidden = true;

            if (expired) {
                expired.hidden = false;
            }
        }
    };

    render();
    const timer = window.setInterval(render, 1000);
}

const statusInputs = document.querySelectorAll('[data-rsvp-status]');
const paxWrap = document.querySelector('[data-pax-wrap]');
const paxInput = document.querySelector('[data-pax-input]');

const syncRsvp = () => {
    const selected = document.querySelector('[data-rsvp-status]:checked');
    const attending = selected?.value === 'hadir';

    if (paxWrap) {
        paxWrap.hidden = !attending;
    }

    if (paxInput) {
        paxInput.disabled = !attending;

        if (!attending) {
            paxInput.value = '0';
        } else if (paxInput.value === '0' || paxInput.value === '') {
            paxInput.value = '1';
        }
    }
};

statusInputs.forEach((input) => input.addEventListener('change', syncRsvp));

if (statusInputs.length > 0) {
    syncRsvp();
}

const audio = document.querySelector('[data-music]');
const youtubeHost = document.querySelector('[data-youtube]');
const musicButton = document.querySelector('[data-music-toggle]');

if (musicButton && (audio || youtubeHost)) {
    const playIcon = musicButton.querySelector('[data-icon-play]');
    const pauseIcon = musicButton.querySelector('[data-icon-pause]');
    const videoId = youtubeHost?.getAttribute('data-youtube') || '';
    let attempted = false;
    let youtubeReady = false;
    let youtubePlayer = null;
    let youtubePending = false;

    const setState = (playing) => {
        musicButton.dataset.state = playing ? 'playing' : 'paused';
        musicButton.setAttribute('aria-label', playing ? 'Hentikan muzik' : 'Mainkan muzik');

        if (playIcon) {
            playIcon.hidden = playing;
        }

        if (pauseIcon) {
            pauseIcon.hidden = !playing;
        }
    };

    const disarm = () => {
        if (attempted) {
            return;
        }

        attempted = true;
        document.removeEventListener('pointerdown', onFirstInteraction);
    };

    const youtubePlaying = () => {
        if (!youtubePlayer || !youtubeReady || !window.YT?.PlayerState) {
            return false;
        }

        const state = youtubePlayer.getPlayerState();

        return state === window.YT.PlayerState.PLAYING || state === window.YT.PlayerState.BUFFERING;
    };

    const playYoutube = () => {
        if (!youtubeReady || !youtubePlayer) {
            youtubePending = true;

            return;
        }

        youtubePending = false;
        youtubePlayer.playVideo();
    };

    const pauseYoutube = () => {
        youtubePending = false;

        if (youtubeReady && youtubePlayer) {
            youtubePlayer.pauseVideo();
        }
    };

    const startPlayback = () => {
        if (youtubeHost) {
            playYoutube();

            return;
        }

        audio.play().catch(() => {});
    };

    const onFirstInteraction = (event) => {
        const target = event.target;
        const pressedToggle = target instanceof Element && target.closest('[data-music-toggle]');
        disarm();

        if (pressedToggle) {
            return;
        }

        startPlayback();
    };

    document.addEventListener('pointerdown', onFirstInteraction);

    if (audio) {
        audio.addEventListener('play', () => setState(true));
        audio.addEventListener('pause', () => setState(false));
    }

    if (youtubeHost && videoId) {
        window.onYouTubeIframeAPIReady = () => {
            youtubePlayer = new window.YT.Player(youtubeHost, {
                videoId,
                width: '200',
                height: '200',
                playerVars: {
                    autoplay: 0,
                    controls: 0,
                    disablekb: 1,
                    fs: 0,
                    loop: 1,
                    modestbranding: 1,
                    playlist: videoId,
                    playsinline: 1,
                    rel: 0,
                },
                events: {
                    onReady: () => {
                        youtubeReady = true;

                        if (youtubePending) {
                            playYoutube();
                        }
                    },
                    onStateChange: (event) => {
                        const state = event.data;
                        const playing = state === window.YT.PlayerState.PLAYING;
                        const stopped = state === window.YT.PlayerState.PAUSED
                            || state === window.YT.PlayerState.CUED
                            || state === window.YT.PlayerState.UNSTARTED;

                        if (playing) {
                            setState(true);
                        } else if (stopped) {
                            setState(false);
                        }
                    },
                    onError: () => {
                        youtubePending = false;
                        setState(false);
                    },
                },
            });
        };

        const api = document.createElement('script');
        api.src = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(api);
    }

    musicButton.addEventListener('click', (event) => {
        event.stopPropagation();
        disarm();

        if (youtubeHost) {
            if (youtubePlaying()) {
                pauseYoutube();
            } else {
                playYoutube();
            }

            return;
        }

        if (audio.paused) {
            audio.play().catch(() => {});
        } else {
            audio.pause();
        }
    });
}

const formError = document.querySelector('[data-form-error]');

if (formError) {
    formError.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
