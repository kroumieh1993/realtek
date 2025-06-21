'use strict';

module.exports = {
    options: {
        // more options here if you want to override JSHint defaults
        globals: {
            jQuery: true
        },
    },
    src: [
        'admin/js/migration.js',
        'admin/js/admin.js',
        'admin/js/settings.js',
        'admin/js/demo.js',
        'admin/js/fields-builder.js',
        'admin/js/data-manager.js',
        'admin/js/property-metabox.js',
        'admin/js/agent-metabox.js',
        'admin/js/entities-list.js',
        'admin/js/subscriptions.js',
        'public/js/public.js',
        'public/js/ajax-entities.js',
        'public/js/gm-popup.js',
        'public/js/elementor.js',
        'public/js/management.js',
        'public/js/subscriptions.js',
    ],
    dest: [
        '<%= adminPath %>/js/migration.js',
        '<%= adminPath %>/js/admin.js',
        '<%= adminPath %>/js/settings.js',
        '<%= adminPath %>/js/demo.js',
        '<%= adminPath %>/js/fields-builder.js',
        '<%= adminPath %>/js/data-manager.js',
        '<%= adminPath %>/js/property-metabox.js',
        '<%= adminPath %>/js/agent-metabox.js',
        '<%= adminPath %>/js/entities-list.js',
        '<%= adminPath %>/js/subscriptions.js',
        '<%= publicPath %>/js/public.js',
        '<%= publicPath %>/js/ajax-entities.js',
        '<%= publicPath %>/js/gm-popup.js',
        '<%= publicPath %>/js/elementor.js',
        '<%= publicPath %>/js/management.js',
        '<%= publicPath %>/js/subscriptions.js'
    ],
    grunt: ['Gruntfile.js']
};
