
	function load() {
		// initial (loading) animations
		setTimeout( function() {
			$("body div.splash .bar").animate({width: '30%'}, 300);
		}, 300);
		setTimeout( function() {
			setTimeout( function() {
				$("body div.splash .progress").animate({opacity:0},{duration: 300, queue: false});
			}, 700);
			setTimeout( function() {
				$("body > div.splash img").animate({width:1000},{duration: 1000, queue: false});
				$("body > div.splash").animate({opacity:0},{duration: 600, queue: false});
				start();
				setTimeout(function() {
					$("body div.splash").remove();
				}, 700)
			}, 1700);
			$("body div.splash .bar").animate({width: '100%'}, {duration: 1000, queue: false});
		}, 1200);

		// handler for both segmented control elements
		$('div.segmented-control a').on('click', function(){
	        $(this).parents('div.segmented-control').first().find('a').each(function(i,e){
	            $(e).removeClass('active');
	        });
	        
	        $(this).addClass('active');
	        $(this).find('input').prop('checked',true);
	        $(document.tipsy).submit()
	        return false;
	    });
	}

	function start() {
		// autofocus on hero textfield
		$("#amount").focus();

		// default selection
	    $('div.segmented-control a:first-of-type').click();
	}

	function adjustMinWidth(el) {
		// to auto-size the custom value fields (within segmented control elements)
		if(el.value == "") {
			$(el).addClass("min-width");
		} else {
			$(el).removeClass("min-width");
		}
	}

	function showTotal() {
		$("#total").animate({opacity: 1}, 1000);
	}

	function hideTotal() {
		$("#total").animate({opacity: 0}, 1000);
	}

	var fetching; // setTimeout result object
	var current = {amount: null, tip: null, people: null};
	var now = current;

	function submitAjax() {
		fetching = window.clearTimeout(fetching);
		now = {amount: parseInt($("#amount").val()), tip: document.tipsy.tip.value, people: document.tipsy.people.value};
		if(now.tip == "custom") now.tip = $("#percent").val();
		if(now.people == "custom") now.people = $("#people").val();
		now.tip = parseInt(now.tip);
		now.people = parseInt(now.people);		
		if(now.amount <= 0 || isNaN(now.amount)) {
			$("#error").fadeOut();
			fetching = setTimeout(hideTotal, 1000);
			return;
		}
		if(now.amount > 0 && now.tip > 0 && now.people > 0 && JSON.stringify(current) != JSON.stringify(now)) {
			current = now;
			console.log("updating...");
			$("#error").fadeOut();
			$("div.sk-cube-grid").fadeIn();
	 		$("#result").fadeOut();
			showTotal();
			fetching = setTimeout(fetch, 500);
		} else {
			current = now;
			var completion = function(){}
			if(now.tip <= 0 || isNaN(now.tip)) {
				completion = function() {
					$("#error").html("type in a percent tip");
					if($("#people").is(":focus") && (now.people <= 0 || isNaN(now.people))) {
						$("#error").html("type in a number of people");
					}
					$("#error").fadeIn();
				}
			} else if (now.people <= 0 || isNaN(now.people)) {
				completion = function() {
					$("#error").html("type in a number of people");
					$("#error").fadeIn();
				}
			} else {
				return
			}
			$("#result").fadeOut();
			if($("#error").is(":visible")) {
				$("#error").fadeOut(completion);
			} else {
				completion();
			}
			
		}
	}

	function fetch() {
		$.post( "?ajax", $(document.tipsy).serialize(), function(data) {
			var json;
			try {
				json = $.parseJSON(data);
			} catch (e) {
				alert("An unexpected error occurred: server returned a malformed response.");
				return;
			}
			if(typeof json.error != "undefined") {
				$("div.sk-cube-grid").fadeOut();
				$("#error").html("error:" + json.error);
				$("#error").fadein();
				return;
			}
			$("#result .grand-total").html(json['grand-total']);
			$("#result .per-head").html(json['per-head']);
			$("div.sk-cube-grid").fadeOut();
			$("#result").fadeIn();
		});
	}