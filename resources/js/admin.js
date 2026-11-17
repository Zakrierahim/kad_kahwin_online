const sidebar = document.querySelector('[data-sidebar]');
const overlay = document.querySelector('[data-sidebar-overlay]');

const closeSidebar = () => {
    sidebar?.classList.add('-translate-x-full');
    overlay?.classList.add('hidden');
};

const openSidebar = () => {
    sidebar?.classList.remove('-translate-x-full');
    overlay?.classList.remove('hidden');
};

document.querySelector('[data-sidebar-open]')?.addEventListener('click', openSidebar);
document.querySelector('[data-sidebar-close]')?.addEventListener('click', closeSidebar);
overlay?.addEventListener('click', closeSidebar);

const contactList = document.querySelector('[data-contact-list]');
const addContact = document.querySelector('[data-add-contact]');
const contactTemplate = document.querySelector('#contact-template');

const reindexContacts = () => {
    contactList?.querySelectorAll('[data-contact-row]').forEach((row, index) => {
        const name = row.querySelector('[data-contact-name]');
        const phone = row.querySelector('[data-contact-phone]');

        if (name) {
            name.name = `contacts[${index}][name]`;
        }

        if (phone) {
            phone.name = `contacts[${index}][phone]`;
        }
    });
};

addContact?.addEventListener('click', () => {
    if (!contactTemplate || !contactList) {
        return;
    }

    contactList.append(contactTemplate.content.cloneNode(true));
    reindexContacts();
});

contactList?.addEventListener('click', (event) => {
    const button = event.target.closest('[data-remove-contact]');

    if (!button) {
        return;
    }

    button.closest('[data-contact-row]')?.remove();
    reindexContacts();
});

const slug = document.querySelector('#slug');
const groom = document.querySelector('#groom_short_name');
const bride = document.querySelector('#bride_short_name');

if (slug && groom && bride) {
    let manual = slug.dataset.manual === '1' || slug.value.trim() !== '';

    const slugify = (value) => value
        .toLowerCase()
        .trim()
        .replace(/['"]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    const syncSlug = () => {
        if (manual) {
            return;
        }

        const next = slugify(`${groom.value}-dan-${bride.value}`);
        slug.value = next === 'dan' ? '' : next;
    };

    slug.addEventListener('input', () => {
        manual = true;
    });

    groom.addEventListener('input', syncSlug);
    bride.addEventListener('input', syncSlug);
}

const themePresets = document.querySelector('#theme-presets');

if (themePresets) {
    const presets = JSON.parse(themePresets.textContent || '{}');
    const primary = document.querySelector('#primary_color');
    const secondary = document.querySelector('#secondary_color');
    const background = document.querySelector('#background_color');
    const primaryPicker = document.querySelector('#primary_color_picker');
    const secondaryPicker = document.querySelector('#secondary_color_picker');
    const backgroundPicker = document.querySelector('#background_color_picker');
    const font = document.querySelector('#font');
    const floral = document.querySelector('#floral_style');

    const bindColor = (picker, field) => {
        if (!picker || !field) {
            return;
        }

        picker.addEventListener('input', () => {
            field.value = picker.value.toUpperCase();
        });

        field.addEventListener('input', () => {
            if (/^#[0-9A-Fa-f]{6}$/.test(field.value)) {
                picker.value = field.value;
            }
        });
    };

    bindColor(primaryPicker, primary);
    bindColor(secondaryPicker, secondary);
    bindColor(backgroundPicker, background);

    document.querySelectorAll('input[name="theme"]').forEach((input) => {
        input.addEventListener('change', () => {
            const preset = presets[input.value];

            if (!preset) {
                return;
            }

            if (primary && primaryPicker) {
                primary.value = preset.primary_color;
                primaryPicker.value = preset.primary_color;
            }

            if (secondary && secondaryPicker) {
                secondary.value = preset.secondary_color;
                secondaryPicker.value = preset.secondary_color;
            }

            if (background && backgroundPicker) {
                background.value = preset.background_color;
                backgroundPicker.value = preset.background_color;
            }

            if (font) {
                font.value = preset.font;
            }

            if (floral) {
                floral.value = preset.floral_style;
            }
        });
    });
}

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy]');

    if (!button) {
        return;
    }

    const value = button.getAttribute('data-copy') || '';
    const original = button.textContent;

    try {
        await navigator.clipboard.writeText(value);
        button.textContent = 'Disalin';
        window.setTimeout(() => {
            button.textContent = original;
        }, 1600);
    } catch (error) {
        window.prompt('Salin pautan ini:', value);
    }
});

document.querySelectorAll('form[data-loading]').forEach((form) => {
    form.addEventListener('submit', () => {
        const button = form.querySelector('[type="submit"]');

        if (!button) {
            return;
        }

        button.disabled = true;
        button.textContent = 'Sedang diproses...';
    });
});
