export default {
  data() {
    return {
      lightboxVisible: false,
      lightboxUrls: [],
      lightboxIndex: 0,
    };
  },
  methods: {
    onContentClick(e) {
      if (e.target && e.target.tagName === 'IMG') {
        const container = e.currentTarget;
        const group = e.target.getAttribute('data-lightbox-group');
        const selector = group
          ? `img[data-lightbox-group="${group}"]`
          : 'img:not([data-lightbox-group])';
        const imgs = Array.from(container.querySelectorAll(selector));
        this.lightboxUrls = imgs.map((img) => img.src);
        this.lightboxIndex = imgs.indexOf(e.target);
        this.lightboxVisible = true;
      }
    },
    closeLightbox() {
      this.lightboxVisible = false;
    },
  },
};
