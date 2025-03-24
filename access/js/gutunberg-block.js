const { registerBlockType } = wp.blocks;
const { useState, useEffect } = wp.element;
const { SelectControl, Button } = wp.components;

registerBlockType('odds-comparison/api-data-block', {
    title: 'Odds Comparison Block',
    icon: 'chart-line',
    category: 'widgets',

    attributes: {
        selectedBookmakers: {
            type: 'array',
            default: []
        },
        bookmakersList: {
            type: 'array',
            default: aoc_ajax.bookmakers // Replace with actual bookmakers
        }
    },

    edit: (props) => {
        const { attributes, setAttributes } = props;
        const { selectedBookmakers, bookmakersList } = attributes;

        const fetchOdds = () => {
            fetch(`${aoc_ajax.ajax_url}?action=fetch_odds`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log(data.data); // Display the fetched odds
                    }
                });
        };

        return wp.element.createElement(
            'div',
            null,
            wp.element.createElement(SelectControl, {
                multiple: true,
                label: "Select Bookmakers",
                value: selectedBookmakers,
                options: bookmakersList.map(bookmaker => ({ label: bookmaker, value: bookmaker })),
                onChange: (selected) => setAttributes({ selectedBookmakers: selected })
            }),
            wp.element.createElement(Button, {
                isPrimary: true,
                onClick: fetchOdds
            }, 'Fetch Odds'),
            wp.element.createElement('div', { id: 'odds-data' })
        );
    },

    save: () => {
        return null; // Dynamic rendering
    },
});
