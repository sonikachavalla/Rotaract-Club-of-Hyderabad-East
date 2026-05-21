// ============================================================
//  firebase-config.js
//  ⚠️  PASTE YOUR FIREBASE CONFIG HERE — used by ALL pages
//
//  Get it from:
//  Firebase Console → ⚙️ Project Settings → Your apps → Web app
// ============================================================

import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
import { getFirestore }  from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";
import { getAuth }       from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

// ▼▼▼ REPLACE THESE 6 VALUES WITH YOUR OWN ▼▼▼
const firebaseConfig = {
  apiKey: "AIzaSyDmo4lOSCygeUTqqapSDKJmYyMV2BWPY5U",
  authDomain: "rche-bfbee.firebaseapp.com",
  projectId: "rche-bfbee",
  storageBucket: "rche-bfbee.firebasestorage.app",
  messagingSenderId: "881384272453",
  appId: "1:881384272453:web:826b2d8ad421923e55292c",
  measurementId: "G-YDCKP4B35T"
};
// ▲▲▲ REPLACE THESE 6 VALUES WITH YOUR OWN ▲▲▲

// Guard: warn loudly if config was never updated
if (firebaseConfig.apiKey.startsWith("PASTE_")) {
  document.body.innerHTML = `
    <div style="font-family:sans-serif;padding:40px;background:#FFF5F5;border:2px solid red;margin:40px;border-radius:12px">
      <h2 style="color:red">⚠️ Firebase not configured</h2>
      <p>Open <strong>firebase-config.js</strong> and replace the placeholder values with your real Firebase project credentials.</p>
      <p>Get them from: Firebase Console → Project Settings → Your apps → Web app</p>
    </div>`;
  throw new Error("Firebase config not set in firebase-config.js");
}

const app = initializeApp(firebaseConfig);
export const db   = getFirestore(app);
export const auth = getAuth(app);
export default app;
