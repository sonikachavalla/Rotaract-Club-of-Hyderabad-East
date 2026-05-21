// ============================================================
//  services/storage.js  —  Firebase Storage upload helpers
//  Images are stored in Cloud Storage and referenced by URL
//  in Firestore — much better than base64 in localStorage.
// ============================================================

import {
  ref, uploadBytes, getDownloadURL, deleteObject
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-storage.js";
import { storage } from "../config/firebase.js";

/**
 * Compress an image file to a max dimension and quality,
 * then return a Blob. Keeps images under ~150KB for fast loads.
 */
export function compressImage(file, maxW = 1200, maxH = 900, quality = 0.82) {
  return new Promise((resolve) => {
    const reader = new FileReader();
    reader.onload = ev => {
      const img = new Image();
      img.onload = () => {
        let w = img.width, h = img.height;
        if (w > maxW || h > maxH) {
          const ratio = Math.min(maxW / w, maxH / h);
          w = Math.round(w * ratio);
          h = Math.round(h * ratio);
        }
        const canvas = document.createElement("canvas");
        canvas.width = w; canvas.height = h;
        canvas.getContext("2d").drawImage(img, 0, 0, w, h);
        canvas.toBlob(blob => resolve(blob), "image/jpeg", quality);
      };
      img.src = ev.target.result;
    };
    reader.readAsDataURL(file);
  });
}

/**
 * Upload an image file to Firebase Storage.
 * Returns the public download URL.
 */
export async function uploadImage(file, path) {
  const blob = await compressImage(file);
  const storageRef = ref(storage, path);
  const snapshot = await uploadBytes(storageRef, blob);
  return await getDownloadURL(snapshot.ref);
}

/**
 * Delete an image from Firebase Storage by its full URL.
 */
export async function deleteImage(url) {
  try {
    const storageRef = ref(storage, url);
    await deleteObject(storageRef);
  } catch (e) {
    // Ignore "not found" errors (file may already be deleted)
    if (e.code !== "storage/object-not-found") throw e;
  }
}

/**
 * Upload a gallery photo. Path: gallery/{timestamp}_{random}.jpg
 */
export async function uploadGalleryPhoto(file) {
  const name = `${Date.now()}_${Math.random().toString(36).slice(2)}.jpg`;
  return uploadImage(file, `gallery/${name}`);
}

/**
 * Upload an event poster. Path: events/{eventId}/poster.jpg
 */
export async function uploadEventPoster(file, eventId) {
  return uploadImage(file, `events/${eventId}/poster.jpg`, 800, 600);
}

/**
 * Upload an event photo. Path: events/{eventId}/photos/{timestamp}.jpg
 */
export async function uploadEventPhoto(file, eventId) {
  const name = `${Date.now()}.jpg`;
  return uploadImage(file, `events/${eventId}/photos/${name}`);
}

/**
 * Upload a team member photo. Path: team/{memberId}/photo.jpg
 */
export async function uploadMemberPhoto(file, memberId) {
  const path = `team/${memberId}/photo.jpg`;
  return uploadImage(file, path, 400, 400, 0.85);
}
