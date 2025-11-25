/**
 * Scripts para Ms Barbearia - Versão 3.2
 * Com todas as animações e melhorias
 */

class MsBarbearia {
  constructor() {
    this.init();
  }

  // 1. Scroll suave para links internos
  smoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', e => {
        e.preventDefault();
        const target = document.querySelector(anchor.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });
  }


  // 3. Status de Abertura Automático
  businessStatus() {
    const updateStatus = () => {
      const now = new Date();
      const hours = now.getHours();
      const day = now.getDay();
      
      const isOpen = day >= 2 && day <= 6 && hours >= 9 && hours < 19;
      const statusElement = document.getElementById('business-status');
      
      if (statusElement) {
        statusElement.innerHTML = isOpen 
          ? '<span class="text-success"><i class="fas fa-door-open me-1"></i>ABERTO AGORA</span>' 
          : '<span class="text-danger"><i class="fas fa-door-closed me-1"></i>FECHADO</span>';
      }
    };

    updateStatus();
    setInterval(updateStatus, 60000);
  }

  // 4. Animação ao Scroll
  scrollAnimations() {
    const animateOnScroll = () => {
      document.querySelectorAll('.animate-on-scroll').forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (elementTop < windowHeight * 0.75) {
          element.classList.add('animate__animated', 'animate__fadeInUp');
        }
      });
    };

    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll();
  }

  // 5. Galeria Interativa (Lightbox)
  gallery() {
    document.querySelectorAll('.gallery-item').forEach(item => {
      item.addEventListener('click', e => {
        e.preventDefault();
        const imgSrc = item.querySelector('img').src;
        const lightboxHTML = `
          <div class="lightbox-overlay">
            <div class="lightbox-content">
              <img src="${imgSrc}" alt="Imagem ampliada">
              <button class="lightbox-close">&times;</button>
            </div>
          </div>`;
        
        document.body.insertAdjacentHTML('beforeend', lightboxHTML);
        
        document.querySelector('.lightbox-overlay').addEventListener('click', e => {
          if (e.target === e.currentTarget || e.target.classList.contains('lightbox-close')) {
            e.currentTarget.remove();
          }
        });
      });
    });
  }



  // 7. Animação de Escrever Texto
  typeWriter() {
    const heroTitle = document.querySelector('.hero-section h1');
    if (!heroTitle) return;

    const text = "Ms Barbearia";
    heroTitle.textContent = '';
    
    let i = 0;
    const typing = setInterval(() => {
      if (i < text.length) {
        heroTitle.textContent += text.charAt(i);
        i++;
      } else {
        clearInterval(typing);
        heroTitle.innerHTML += '<span class="blinking-cursor">|</span>';
      }
    }, 100);
  }

  // 8. Efeito Parallax
  parallaxEffect() {
    if (window.matchMedia("(max-width: 768px)").matches) return;

    const parallaxElements = document.querySelectorAll('[data-parallax]');
    
    window.addEventListener('scroll', () => {
      const scrollPosition = window.pageYOffset;
      
      parallaxElements.forEach(element => {
        const speed = parseFloat(element.dataset.parallax) || 0.5;
        const offset = scrollPosition * speed;
        element.style.transform = `translateY(${offset}px)`;
      });
    });
  }

  // 9. Animação de Ferramentas
  animateTools() {
    const tools = document.querySelectorAll('.barber-tool');
    
    tools.forEach((tool, index) => {
      tool.style.animation = `floatTool ${3 + index * 0.5}s ease-in-out infinite alternate`;
    });
  }

  // 10. Efeito de Neblina Dourada
  goldenHazeEffect() {
    if (document.querySelector('.golden-haze')) return;
    const haze = document.createElement('div');
    haze.className = 'golden-haze';
    document.querySelector('.hero-section').appendChild(haze);
  }

  // 11. Transição Entre Seções
  smoothSectionTransition() {
    const sections = document.querySelectorAll('section');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('section-visible');
        }
      });
    }, { threshold: 0.1 });

    sections.forEach(section => observer.observe(section));
  }

  // 12. Animação de Preços
  animatePrices() {
    const prices = document.querySelectorAll('.price-value');
    
    prices.forEach(price => {
      const originalValue = price.textContent;
      price.textContent = '0';
      
      let current = 0;
      const target = parseInt(originalValue.replace(/\D/g, ''));
      const increment = target / 20;
      
      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          clearInterval(timer);
          price.textContent = originalValue;
        } else {
          price.textContent = Math.floor(current).toLocaleString();
        }
      }, 50);
    });
  }

  // 13. Carrossel de Depoimentos
  testimonialCarousel() {
    const testimonials = document.querySelector('.testimonials-carousel');
    if (!testimonials) return;

    let currentIndex = 0;
    const items = testimonials.querySelectorAll('.testimonial-item');
    
    setInterval(() => {
      items[currentIndex].classList.remove('active');
      currentIndex = (currentIndex + 1) % items.length;
      items[currentIndex].classList.add('active');
    }, 5000);
  }

  // 14. Feed do Instagram
  socialMediaFeed() {
    const instagramFeed = document.getElementById('instagram-feed');
    if (!instagramFeed) return;

    // Simulação de dados - na prática você usaria a API do Instagram
    const instagramData = [
      { media_url: 'img/galeria1.jpg', caption: 'Corte moderno' },
      { media_url: 'img/galeria2.jpg', caption: 'Ambiente premium' }
    ];

    instagramFeed.innerHTML = instagramData.map(post => `
      <div class="col-md-4 mb-4">
        <div class="instagram-post">
          <img src="${post.media_url}" alt="${post.caption}" class="img-fluid">
          <div class="post-overlay">
            <i class="fab fa-instagram"></i>
          </div>
        </div>
      </div>
    `).join('');
  }

  // 15. Pré-carregamento de Recursos
  preloadResources() {
    const resources = [
      'img/background-texture.jpg',
      'img/tesoura.png',
      'img/galeria1.jpg',
      'img/galeria2.jpg'
    ];

    resources.forEach(resource => {
      const link = document.createElement('link');
      link.rel = 'preload';
      link.as = resource.includes('img/') ? 'image' : 'font';
      link.href = resource;
      document.head.appendChild(link);
    });
  }

  // 16. Monitoramento de Performance
  trackPerformance() {
    if ('performance' in window) {
      window.addEventListener('load', () => {
        const timing = performance.timing;
        const loadTime = timing.loadEventEnd - timing.navigationStart;
        
        if (loadTime > 3000) {
          console.warn(`Tempo de carregamento lento: ${loadTime}ms`);
        }
      });
    }
  }

  // 17. Sistema de Agendamento em Tempo Real
  realTimeBooking() {
    const bookingForm = document.getElementById('booking-form');
    if (!bookingForm) return;
    
    bookingForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const submitBtn = bookingForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Agendando...';
      submitBtn.disabled = true;
      
      try {
        await new Promise(resolve => setTimeout(resolve, 1500));
        this.showNotification('success', 'Agendamento confirmado!');
        bookingForm.reset();
      } catch (error) {
        this.showNotification('error', 'Ocorreu um erro. Tente novamente.');
      } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    });
  }

  // 18. Mostrar Notificações
  showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
      <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
      ${message}
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.classList.add('show');
      setTimeout(() => notification.remove(), 3000);
    }, 100);
  }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => new MsBarbearia());