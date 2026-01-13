(function(wp) {
    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.editor.InspectorControls;
    var TextControl = wp.components.TextControl;
    var SelectControl = wp.components.SelectControl;
    var PanelBody = wp.components.PanelBody;

    registerBlockType('push-notification-integration/notification', {
        title: 'Push Notification',
        icon: 'bell',
        category: 'widgets',
        attributes: {
            notificationId: {
                type: 'number',
                default: 0,
            },
            buttonText: {
                type: 'string',
                default: 'Show Notification',
            },
            roles: {
                type: 'string',
                default: '',
            },
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            // Get notifications for dropdown
            var notifications = wp.apiFetch({
                path: '/wp/v2/push_notification?per_page=100'
            }).then(function(posts) {
                return posts.map(function(post) {
                    return { value: post.id, label: post.title.rendered };
                });
            });

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: 'Notification Settings' },
                        el(SelectControl, {
                            label: 'Select Notification',
                            value: attributes.notificationId,
                            options: [{ value: 0, label: 'Select a notification' }].concat(notifications || []),
                            onChange: function(value) {
                                setAttributes({ notificationId: parseInt(value) });
                            }
                        }),
                        el(TextControl, {
                            label: 'Button Text',
                            value: attributes.buttonText,
                            onChange: function(value) {
                                setAttributes({ buttonText: value });
                            }
                        }),
                        el(TextControl, {
                            label: 'User Roles (comma separated)',
                            value: attributes.roles,
                            onChange: function(value) {
                                setAttributes({ roles: value });
                            }
                        })
                    )
                ),
                el('div', { className: props.className },
                    el('button', { className: 'push-notification-btn' }, attributes.buttonText || 'Show Notification')
                )
            ];
        },

        save: function(props) {
            return null; // Dynamic block
        },
    });
})(window.wp);