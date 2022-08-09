CKEDITOR.plugins.add('fixed_toolbar', {
  init: (editor) => {
    CKEDITOR.on('instanceReady', (e) => {
      if (null === e.editor.container.find('.cke_top').getItem(0)) {
        return;
      }
      const toolBar = e.editor.container.find('.cke_top').getItem(0).$;
      toolBar.style.position = 'sticky';

      const dialog = jQuery('.MuiDialogTitle-root');
      // We use the same offset as Drupal uses for body to make up for Admin
      // Toolbar.
      toolBar.style.top = document.body.style.paddingTop;
    });
  },
});
