class Utils {
  // Image loader wrapper to handle async/await
  static loadImage(url, el) {
    return new Promise((resolve, reject) => {
      el.onload = () => resolve(el);
      el.onerror = reject;
      el.src = url;
    });
  }
}

// Service for handling HTTP requests
class ApiService {
  async getImageId() {
    const response = await fetch('/main.php?action=get_image_id');
    let result = await response.json();

    return result?.data?.id || null;
  }

  async increaseCount(imageId) {
    await fetch(`/main.php?action=increase_count&image_id=${imageId}`, {
      method: 'POST'
    });
  }

  async getCount(imageId) {
    const response = await fetch(`/main.php?action=get_count&image_id=${imageId}`);
    let result = await response.json();

    return result?.data?.views_count || null;
  }
}

class App {
  imageId = null;
  count = 0;

  constructor() {
    this.apiService = new ApiService();
  }

  // Update the count value
  async updateCountValue() {
    const count = await this.apiService.getCount(this.imageId);

    if (this.count === count || !count) {
      return;
    }

    this.count = count;
    this.countEl.textContent = this.count;
  }

  // Start a timer to refresh the count value from API
  startTimer() {
    const TIMEOUT = 5000;

    setInterval(() => {
      this.updateCountValue();
    }, TIMEOUT);
  }

  // Handle successful image loading
  async handleImageLoad() {
    if (!this.imageId) {
      return;
    }

    this.apiService.increaseCount(this.imageId);
    this.updateCountValue();
    this.startTimer();
  }

  // Handle image loading error
  handleImageError() {
    this.imgEl.src = '/src/empty.jpg';
  }

  // Start application
  async init() {
    this.countEl = document.querySelector('#value');
    this.imgEl = document.querySelector('#image');
    this.imageId = await this.apiService.getImageId();

    try {
      await Utils.loadImage(`/src/${this.imageId}.jpg`, this.imgEl);
      this.handleImageLoad();
    } catch (e) {
      this.handleImageError();
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const app = new App();

  app.init();
});
