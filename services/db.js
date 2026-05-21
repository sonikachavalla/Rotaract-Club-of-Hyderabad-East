// ============================================================
//  services/db.js  —  Clean reusable Firestore service layer
//  All CRUD operations for every collection go through here.
// ============================================================

import {
  collection, doc, getDocs, getDoc, addDoc, setDoc,
  updateDoc, deleteDoc, onSnapshot, query, orderBy,
  serverTimestamp, writeBatch
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";
import { db } from "../config/firebase.js";

// ── Collection names ─────────────────────────────────────────
export const COLLECTIONS = {
  EVENTS:   "events",
  TEAM:     "team",
  GALLERY:  "gallery",
  SETTINGS: "settings",
  PDFS:     "pdfs",
  LOG:      "activityLog",
  YEARS:    "pdfYears"
};

// ── Generic helpers ───────────────────────────────────────────

/** Fetch all documents from a collection, returns array of {id, ...data} */
export async function getAll(collName) {
  const snap = await getDocs(collection(db, collName));
  return snap.docs.map(d => ({ id: d.id, ...d.data() }));
}

/** Get a single document by ID */
export async function getOne(collName, docId) {
  const snap = await getDoc(doc(db, collName, docId));
  return snap.exists() ? { id: snap.id, ...snap.data() } : null;
}

/** Add a new document (auto-ID) */
export async function addItem(collName, data) {
  const ref = await addDoc(collection(db, collName), {
    ...data,
    createdAt: serverTimestamp()
  });
  return ref.id;
}

/** Set a document with a known ID (creates or overwrites) */
export async function setItem(collName, docId, data) {
  await setDoc(doc(db, collName, docId), {
    ...data,
    updatedAt: serverTimestamp()
  }, { merge: true });
}

/** Update specific fields on a document */
export async function updateItem(collName, docId, data) {
  await updateDoc(doc(db, collName, docId), {
    ...data,
    updatedAt: serverTimestamp()
  });
}

/** Delete a document */
export async function deleteItem(collName, docId) {
  await deleteDoc(doc(db, collName, docId));
}

// ── Real-time listeners ───────────────────────────────────────

/** Subscribe to a collection in real-time. Returns unsubscribe function. */
export function subscribe(collName, callback) {
  return onSnapshot(collection(db, collName), snap => {
    const docs = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    callback(docs);
  });
}

/** Subscribe to a single document in real-time. Returns unsubscribe function. */
export function subscribeDoc(collName, docId, callback) {
  return onSnapshot(doc(db, collName, docId), snap => {
    callback(snap.exists() ? { id: snap.id, ...snap.data() } : null);
  });
}

// ── Settings (single "global" doc) ───────────────────────────

export async function getSettings() {
  const snap = await getDoc(doc(db, COLLECTIONS.SETTINGS, "global"));
  if (snap.exists()) return snap.data();
  // Return defaults if not set
  return {
    clubname:  "Rotaract Club of Hyderabad East",
    email:     "rchyderabadeast@gmail.com",
    phone:     "+91 99511 43775",
    instagram: "https://www.instagram.com/rchyderabadeast",
    linkedin:  "https://www.linkedin.com/company/rotaract-club-of-hyderabad-east/",
    joinform:  "https://docs.google.com/forms/d/e/1FAIpQLSeJU6e7tgw97S5mSkZkR2-9IyYKRAs1qU9m6-kW6QkzhjderA/viewform"
  };
}

export async function saveSettings(data) {
  await setDoc(doc(db, COLLECTIONS.SETTINGS, "global"), {
    ...data,
    updatedAt: serverTimestamp()
  }, { merge: true });
}

export function subscribeSettings(callback) {
  return subscribeDoc(COLLECTIONS.SETTINGS, "global", callback);
}

// ── PDF Years (single doc holding the array) ──────────────────

export async function getPdfYears() {
  const snap = await getDoc(doc(db, COLLECTIONS.YEARS, "list"));
  return snap.exists() ? (snap.data().years || []) : ["2024-2025", "2023-2024", "2022-2023"];
}

export async function savePdfYears(years) {
  await setDoc(doc(db, COLLECTIONS.YEARS, "list"), { years, updatedAt: serverTimestamp() });
}

// ── Activity log (append-only, capped at 10) ─────────────────

export async function logActivity(msg) {
  const ts = new Date().toLocaleString("en-IN", {
    day: "2-digit", month: "short", hour: "2-digit", minute: "2-digit"
  });
  await addDoc(collection(db, COLLECTIONS.LOG), { msg, ts, createdAt: serverTimestamp() });
}

export async function getRecentLog(limit = 10) {
  const snap = await getDocs(
    query(collection(db, COLLECTIONS.LOG), orderBy("createdAt", "desc"))
  );
  return snap.docs.slice(0, limit).map(d => ({ id: d.id, ...d.data() }));
}
