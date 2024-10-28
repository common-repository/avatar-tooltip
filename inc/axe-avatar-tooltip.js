var $axeAT = jQuery.noConflict();
$axeAT (document).ready ( function(){

		$axeAT( axeATloc.qtipOptions.jq_selector ).each(function() {

			// Delete title link that could be over the tooltip
			$axeAT(this).parent('a').attr('title','');

			// Rollover on avatar
			$axeAT(this).mouseenter(function() {
				$axeAT(this).addClass('avatar-active');
			}).mouseleave(function() {
				$axeAT(this).removeClass('avatar-active');
			});

			// Be sure that there is the REl attr added by plugin
			var attrRel = $axeAT(this).attr('rel');

			if ( typeof attrRel !== 'undefined' && attrRel !== false ) {
					
				$axeAT(this).qtip({	
					style: {
							classes: 'ui-tooltip-'+ axeATloc.qtipOptions.style_class +' ui-tooltip-rounded ui-tooltip-shadow axe-avatar-tooltip'
					},
					position: {
						my: axeATloc.qtipOptions.position_my,
						at: axeATloc.qtipOptions.position_at
					},
					hide: {
						/*event: false,
						inactive: 5000*/
						delay:	1000,
						fixed: true
					},
					show: {			
						solo: true,
						event: axeATloc.qtipOptions.show_event,
						delay: 500
					},
					content: {
						text: '<span class="preloading"><img src="'+ axeATloc.scriptPath +'images/loading.gif" alt="'+ axeATloc.loadingTxt +'" /></span>',
						ajax: {
							url: axeATloc.ajaxurl,
							type: 'POST',
							data: {
									action		:	'axe_at_get_tooltip_content',
									uid			:	attrRel,
									_ajax_nonce	: 	axeATloc.nonce
							},
							dataType: 'json',
							//contentType: "application/json; charset=utf-8",
							success: function(data, status) {
								// Set the content manually (required!)
								if ( status == 'success' ) {
									this.set( 'content.title.text', data.ttTitle );
									this.set( 'content.text', data.ttContent );
								}
							},
							error: function(message) {
								this.set( 'content.text', axeATloc.errorTxt );
							}						
						},
						title: {
							//text: axeATloc.tipTitle,
							button: axeATloc.closeTxt
						}
					}
				});
				
			} // if attr('rel')
			
		});
	
			
});
