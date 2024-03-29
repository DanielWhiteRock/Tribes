var Widget_DCE_Dynamicposts_grid_Handler = function ($scope, $) {
    var smsc = null;
	var elementSettings = dceGetElementSettings($scope);
    var id_scope = $scope.attr('data-id');
	var grid = $scope.find('.dce-posts-container.dce-skin-grid .dce-posts-wrapper');
    var masonryGrid = null;
    var isMasonryEnabled = false;
	let byRow = elementSettings.grid_match_height_by_row || elementSettings.grid_filters_match_height_by_row;

	// MASONRY
	function activeMasonry(){
	    masonryGrid = grid.masonry({
		  itemSelector: '.dce-post-item',
		});
		isMasonryEnabled = true;
	}
	function layoutMasonry(){
		if( elementSettings[dceDynamicPostsSkinPrefix+'grid_type'] != 'masonry' ){
			masonryGrid.masonry('destroy');
			isMasonryEnabled = false;
		}else{
			masonryGrid.masonry();
		}
	}

	if( elementSettings.grid_match_height || elementSettings.grid_filters_match_height ) {
		if( elementSettings.style_items === 'template' ) {
			if( $scope.find( '.dce-post-block .elementor-inner-section' ).length ) {
				$scope.find('.dce-post-block').first().find('.elementor-inner-section').each((i) => {
					let $els = $scope.find('.dce-post-block').map((_,$e) => {
						return jQuery($e).find('.elementor-inner-section')[i]
					})
					$els.matchHeight( {byRow: byRow} )
					$matchHeightEls = $els;
				});
			} else {
				selector = '.dce-post-block .elementor-top-section';
				$matchHeightEls = $(selector);
				$matchHeightEls.matchHeight({byRow: byRow});
			}
		} else {
			selector = '.dce-post-block';
			$matchHeightEls = $(selector);
			$matchHeightEls.matchHeight({byRow: byRow});
		}
	}

	if(smsc) {
		smsc.remove();
	}

	if( elementSettings[dceDynamicPostsSkinPrefix+'grid_type'] == 'masonry' ) {
		activeMasonry();
	}

  	// InfiniteScroll
	if (elementSettings.infiniteScroll_enable){
        var elementorElement = '.elementor-element-' + id_scope;
        var is_history = Boolean( elementSettings.infiniteScroll_enable_history ) ? 'replace' : false;
        var grid_container = $scope.find('.dce-posts-container.dce-skin-grid .dce-posts-wrapper.dce-wrapper-grid');
        var $layoutMode = elementSettings[dceDynamicPostsSkinPrefix+'grid_type'];
        var $grid = grid_container.isotope({
            itemSelector: '.dce-post-item',
            layoutMode: 'masonry' === $layoutMode ? 'masonry' : 'fitRows',
            sortBy: 'original-order',
            percentPosition: true,
            masonry: {
                columnWidth: '.dce-post-item'
            }
        });

		// Apply link to template when layout is complete
		if(
			false === elementorFrontend.isEditMode() &&
			'yes' === elementSettings.templatemode_linkable
		){
			grid_container.on( 'append.infiniteScroll', function( event, title, path ) {
				$scope.find('.dce-post.dce-post-item[data-post-link]').click(function() {
					window.location.assign( $(this).attr("data-post-link") );
					return false;
				});
			});
		}

		// Match Height when layout is complete
		if( elementSettings.grid_match_height || elementSettings.grid_filters_match_height ) {
			grid_container.on( 'append.infiniteScroll', function( event, title, path ) {
				$matchHeightEls.matchHeight(
					{
						byRow: byRow,
					}
				);
				grid_container.isotope('layout');
			});
		}

		// Reload the template after using Infinite Scroll
		if( 'template' === elementSettings.style_items ) {
			grid_container.on( 'append.infiniteScroll', function( event, title, path ) {
				if ( elementorFrontend) {
					if ( elementorFrontend.elementsHandler.runReadyTrigger ) {
						var widgets = $('.dce-dynamic-posts-collection').find('.elementor-widget');
						widgets.each(function (i) {
							elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
							elementorFrontend.hooks.doAction('frontend/element_ready/global', jQuery(this), jQuery);
						});

					}
				}

				// Add inline CSS for background url
				var allArticles = document.querySelectorAll(".dce-dynamic-posts-collection .elementor-section, .dce-dynamic-posts-collection .elementor-column, .dce-dynamic-posts-collection .elementor-widget, .dce-dynamic-posts-collection .e-container");
				allArticles.forEach(function(article) {
					dce.addCssForBackground( article );
				});
			});

			// When Search&Filter request is completed
			$(document).on("sf:ajaxfinish", ".searchandfilter", function( e, data ) {
				if ( elementorFrontend) {
					// Fix Background Loop
					if ( elementorFrontend.elementsHandler.runReadyTrigger ) {
						// Add inline CSS for background url
						var allArticles = document.querySelectorAll(".dce-dynamic-posts-collection .elementor-section, .dce-dynamic-posts-collection .elementor-column, .dce-dynamic-posts-collection .elementor-widget, .dce-dynamic-posts-collection .e-container");
						allArticles.forEach(function(article) {
							dce.addCssForBackground( article );
						});
					}
					// Match Height
					$matchHeightEls.matchHeight(
						{
							byRow: byRow,
						}
					);
					// Template Linkable
					$scope.find('.dce-post.dce-post-item[data-post-link]').click(function() {
						window.location.assign( $(this).attr("data-post-link") );
						return false;
					});
				}
			});
		}

        var iso = $grid.data('isotope');

        if (jQuery(elementorElement + ' .pagination__next').length) {
            var infiniteScroll_options = {
                path: elementorElement + ' .pagination__next',
                history: is_history,
                append: elementorElement + ' .dce-post.dce-post-item',
                outlayer: iso,
                status: elementorElement + ' .page-load-status',
                hideNav: elementorElement + '.pagination',
                scrollThreshold: 'scroll' === elementSettings.infiniteScroll_trigger ? true : false,
                loadOnScroll: 'scroll' === elementSettings.infiniteScroll_trigger ? true : false,
                onInit: function () {
                    this.on('load', function () {
                    });
                }
            }
            if (elementSettings.infiniteScroll_trigger == 'button') {
                // load pages on button click
                infiniteScroll_options['button'] = elementorElement + ' .view-more-button';
            }
            infScroll = grid_container.infiniteScroll(infiniteScroll_options);

            // fix for infinitescroll + masonry
            var nElements = jQuery(elementorElement + ' .dce-post-item:visible').length; // initial length

            grid_container.on( 'append.infiniteScroll', function( event, response, path, items ) {
                setTimeout(function(){
                    var nElementsVisible = jQuery(elementorElement + ' .dce-post-item:visible').length;
                    if (nElementsVisible <= nElements) {
                        // force another load
                        grid_container.infiniteScroll('loadNextPage');
                    }
                }, 1000);
            });
        }
    }

    // Scroll Reveal
    var on_scrollReveal = function(){
		var runRevAnim = function(dir){
        	var el = $( this );
            var i = $( this ).index();

            if(dir == 'down'){
               	setTimeout(function(){
               		el.addClass('animate');
               	}, 100 * i);
                // play
            }else if(dir == 'up'){
                el.removeClass('animate');
                // stop
            }
        };
        var waypointRevOptions = {
            offset: '100%',
            triggerOnce: false
        };
        elementorFrontend.waypoint($scope.find('.dce-post-item'), runRevAnim, waypointRevOptions);

    };
    on_scrollReveal();

	// Callback function executed when mutations occur
	var Dyncontel_MutationObserverCallback = function(mutationsList, observer) {
	    for(var mutation of mutationsList) {
	        if (mutation.type == 'attributes') {
	           if (mutation.attributeName === 'class') {
		            if (isMasonryEnabled) {
				      layoutMasonry();
				    }
		        }
	        }
	    }
	};
	dceObserveElement($scope[0], Dyncontel_MutationObserverCallback);
};

jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.grid', Widget_DCE_Dynamicposts_grid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.grid', Widget_DCE_Dynamicposts_grid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.grid', Widget_DCE_Dynamicposts_grid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.grid', Widget_DCE_Dynamicposts_grid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.grid', Widget_DCE_Dynamicposts_grid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.grid', Widget_DCE_Dynamicposts_grid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.grid', Widget_DCE_Dynamicposts_grid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.grid', Widget_DCE_Dynamicposts_grid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.grid', Widget_DCE_Dynamicposts_grid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.grid', Widget_DCE_Dynamicposts_grid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
});
