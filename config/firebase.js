// ============================================================
//  config/firebase.js
//  Replace the firebaseConfig values with your own from the
//  Firebase Console → Project Settings → Your apps → Web app
// ============================================================

import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
import { getFirestore }  from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";
import { getAuth }       from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";
import { getStorage }    from "https://www.gstatic.com/firebasejs/10.12.2/firebase-storage.js";

// ── REPLACE THESE WITH YOUR OWN FIREBASE PROJECT CONFIG ──────
const firebaseConfig = {
  apiKey: "AIzaSyDmo4lOSCygeUTqqapSDKJmYyMV2BWPY5U",
  authDomain: "rche-bfbee.firebaseapp.com",
  projectId: "rche-bfbee",
  storageBucket: "rche-bfbee.firebasestorage.app",
  messagingSenderId: "881384272453",
  appId: "1:881384272453:web:826b2d8ad421923e55292c",
  measurementId: "G-YDCKP4B35T"
};
// ─────────────────────────────────────────────────────────────

const app     = initializeApp(firebaseConfig);
export const db      = getFirestore(app);
export const auth    = getAuth(app);
export const storage = getStorage(app);
export default app;
