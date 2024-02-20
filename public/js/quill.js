let quill = new Quill('#editor', {
  modules: {
    toolbar: [
      [{ header: [1, 2, false] }],
      ['bold', 'italic', 'underline'],
      ['image', 'code-block'],
    ],
  },
  theme: 'snow',
});

quill.on('text-change', function() {
  let contenu = quill.root.innerHTML;
  document.getElementById('postContent').value = contenu;
});