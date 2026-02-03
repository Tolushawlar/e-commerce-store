/**
 * Image Service
 * Handles image upload and management operations via Cloudinary
 */

class ImageService {
  /**
   * Upload a single image
   * Endpoint: POST /api/images/upload
   * @param {File} file - The file to upload
   * @param {Object} options - Upload options (folder, public_id)
   * @returns {Promise<Object>} Upload result with URL and metadata
   */
  async uploadImage(file, options = {}) {
    try {
      const formData = new FormData();
      formData.append("image", file);

      if (options.folder) {
        formData.append("folder", options.folder);
      }

      if (options.public_id) {
        formData.append("public_id", options.public_id);
      }

      // Use api.uploadFile which handles token refresh automatically
      const result = await api.uploadFile("/api/images/upload", formData);
      return result;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Upload multiple images
   * Endpoint: POST /api/images/upload-multiple
   * @param {FileList|Array<File>} files - Files to upload
   * @param {Object} options - Upload options (folder)
   * @returns {Promise<Object>} Upload results
   */
  async uploadMultiple(files, options = {}) {
    try {
      const formData = new FormData();

      // Add all files
      for (let i = 0; i < files.length; i++) {
        formData.append("images[]", files[i]);
      }

      if (options.folder) {
        formData.append("folder", options.folder);
      }

      // Use api.uploadFile which handles token refresh automatically
      const result = await api.uploadFile(
        "/api/images/upload-multiple",
        formData,
      );
      return result;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Upload image from URL
   * Endpoint: POST /api/images/upload-from-url
   * @param {string} imageUrl - URL of the image to upload
   * @param {Object} options - Upload options (folder)
   * @returns {Promise<Object>} Upload result
   */
  async uploadFromUrl(imageUrl, options = {}) {
    try {
      const response = await api.post("/api/images/upload-from-url", {
        url: imageUrl,
        folder: options.folder,
      });
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Delete an image
   * Endpoint: DELETE /api/images/{publicId}
   * @param {string} publicId - The public_id of the image to delete
   * @returns {Promise<Object>} Deletion result
   */
  async deleteImage(publicId) {
    try {
      const encodedPublicId = encodeURIComponent(publicId);
      const response = await api.delete(`/api/images/${encodedPublicId}`);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get image details
   * Endpoint: GET /api/images/{publicId}/details
   * @param {string} publicId - The public_id of the image
   * @returns {Promise<Object>} Image details
   */
  async getDetails(publicId) {
    try {
      const encodedPublicId = encodeURIComponent(publicId);
      const response = await api.get(`/api/images/${encodedPublicId}/details`);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get transformed image URL
   * Endpoint: POST /api/images/transform
   * @param {string} publicId - The public_id of the image
   * @param {Object} transformations - Transformation options (width, height, crop, quality)
   * @returns {Promise<string>} Transformed image URL
   */
  async getTransformedUrl(publicId, transformations = {}) {
    try {
      const response = await api.post("/api/images/transform", {
        public_id: publicId,
        ...transformations,
      });
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Create a file input and trigger upload
   * @param {Object} options - Upload options
   * @param {Function} onSuccess - Success callback
   * @param {Function} onError - Error callback
   */
  triggerUpload(options = {}, onSuccess, onError) {
    const input = document.createElement("input");
    input.type = "file";
    input.accept = "image/*";
    input.multiple = options.multiple || false;

    input.onchange = async (e) => {
      const files = e.target.files;
      if (!files || files.length === 0) return;

      try {
        let result;
        if (options.multiple) {
          result = await this.uploadMultiple(files, options);
        } else {
          result = await this.uploadImage(files[0], options);
        }

        if (onSuccess) onSuccess(result);
      } catch (error) {
        if (onError) onError(error);
      }
    };

    input.click();
  }

  /**
   * Create preview URL for a file before upload
   * @param {File} file - The file to preview
   * @returns {Promise<string>} Preview URL
   */
  createPreview(file) {
    return new Promise((resolve, reject) => {
      if (!file || !file.type.startsWith("image/")) {
        reject(new Error("Invalid file type"));
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => resolve(e.target.result);
      reader.onerror = reject;
      reader.readAsDataURL(file);
    });
  }

  /**
   * Validate image file
   * @param {File} file - The file to validate
   * @param {Object} constraints - Validation constraints (maxSize, allowedFormats)
   * @returns {Object} Validation result
   */
  validateImage(file, constraints = {}) {
    const maxSize = constraints.maxSize || 5 * 1024 * 1024; // 5MB default
    const allowedFormats = constraints.allowedFormats || [
      "jpg",
      "jpeg",
      "png",
      "gif",
      "webp",
      "svg",
    ];

    const errors = [];

    // Check file size
    if (file.size > maxSize) {
      errors.push(`File size exceeds ${maxSize / (1024 * 1024)}MB`);
    }

    // Check file type
    const extension = file.name.split(".").pop().toLowerCase();
    if (!allowedFormats.includes(extension)) {
      errors.push(`Invalid file format. Allowed: ${allowedFormats.join(", ")}`);
    }

    // Check MIME type
    if (!file.type.startsWith("image/")) {
      errors.push("File must be an image");
    }

    return {
      valid: errors.length === 0,
      errors: errors,
    };
  }
}

// Create global instance
const imageService = new ImageService();
