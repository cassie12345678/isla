/*
 * JavaScript for the Isla Vayne website
 *
 * This script powers the interactive elements of the site such as
 * responsive navigation, theme switching, gallery modals, blog search
 * and the contact form feedback. It is intentionally plain and
 * lightweight so the pages remain fast and easy to maintain.
 */

document.addEventListener('DOMContentLoaded', () => {
  // Responsive navigation toggle for small screens
  const navToggle = document.querySelector('.nav-toggle');
  const navMenu = document.querySelector('.nav-menu');
  if (navToggle && navMenu) {
    navToggle.addEventListener('click', () => {
      navMenu.classList.toggle('open');
    });
  }

  // Theme switcher (light/dark). Persist selection via localStorage
  const themeToggle = document.getElementById('theme-toggle');
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    document.body.classList.add('dark');
  }
  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      document.body.classList.toggle('dark');
      if (document.body.classList.contains('dark')) {
        localStorage.setItem('theme', 'dark');
      } else {
        localStorage.setItem('theme', 'light');
      }
    });
  }

  // Create a reusable modal for gallery images
  const createModal = () => {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'none';
    modal.innerHTML = `
      <div class="modal-content">
        <button class="modal-close" aria-label="Sluiten">&times;</button>
        <img src="" alt="">
        <div class="caption"></div>
      </div>
    `;
    document.body.appendChild(modal);
    return modal;
  };
  const modal = createModal();
  const modalImg = modal.querySelector('img');
  const modalCaption = modal.querySelector('.caption');
  const modalClose = modal.querySelector('.modal-close');

  // Open modal when a gallery figure is clicked
  const galleryFigures = document.querySelectorAll('.gallery-grid figure');
  if (galleryFigures.length && modalImg && modalCaption) {
    galleryFigures.forEach((figure) => {
      figure.addEventListener('click', () => {
        const img = figure.querySelector('img');
        if (!img) return;
        modalImg.src = img.src;
        modalImg.alt = img.alt;
        const captionEl = figure.querySelector('figcaption');
        modalCaption.textContent = captionEl ? captionEl.textContent : '';
        modal.style.display = 'flex';
      });
    });
  }

  // Close modal when close button or overlay is clicked
  if (modalClose) {
    modalClose.addEventListener('click', () => {
      modal.style.display = 'none';
    });
  }
  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });

  // Blog search filter: hides posts whose titles don't match the search term
  const searchInput = document.getElementById('blog-search');
  if (searchInput) {
    const posts = document.querySelectorAll('.blog-post');
    searchInput.addEventListener('input', () => {
      const term = searchInput.value.toLowerCase();
      posts.forEach((post) => {
        const h3 = post.querySelector('h3');
        const title = h3 ? h3.textContent.toLowerCase() : '';
        if (title.includes(term)) {
          post.style.display = '';
        } else {
          post.style.display = 'none';
        }
      });
    });
  }

  // Contact form submission feedback
  const contactForm = document.getElementById('contact-form');
  const formMessage = document.getElementById('form-message');
  if (contactForm && formMessage) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      // Provide a friendly confirmation message in Dutch
      formMessage.textContent = 'Bedankt voor je bericht! We nemen zo snel mogelijk contact met je op.';
      formMessage.style.display = 'block';
      // Reset the form fields
      contactForm.reset();
    });
  }

  // Age verification modal: show if not previously confirmed
  const ageModal = document.getElementById('age-modal');
  const ageConfirmBtn = document.getElementById('age-confirm-btn');
  // Only run this if the modal exists on the page
  if (ageModal && !localStorage.getItem('ageConfirmed')) {
    // Show the modal overlay
    ageModal.style.display = 'flex';
    if (ageConfirmBtn) {
      ageConfirmBtn.addEventListener('click', () => {
        // Hide the modal and persist confirmation when clicking via JS
        confirmAge();
      });
    }
  }
});

/**
 * Hide the age verification modal and persist the confirmation.
 * This function is exposed on the global window object so it can be
 * invoked from inline HTML (e.g. onclick handlers). If the modal
 * element is present it will be hidden, and the flag stored in
 * localStorage ensures the prompt is not shown again on subsequent
 * page loads for the same origin.
 */
function confirmAge() {
  const modal = document.getElementById('age-modal');
  if (modal) {
    modal.style.display = 'none';
  }
  try {
    localStorage.setItem('ageConfirmed', 'true');
  } catch (e) {
    // Ignore storage errors (e.g. localStorage not available)
  }
}
// Expose confirmAge globally so inline onclick attributes work
window.confirmAge = confirmAge;