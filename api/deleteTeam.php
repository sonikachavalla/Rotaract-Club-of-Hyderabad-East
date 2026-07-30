await window._fb.deleteDoc(
    window._fb.doc(
        window._fb.db,
        "team",
        pendingDelete.id
    )
);

toast("Team member deleted!");

await loadTeam();