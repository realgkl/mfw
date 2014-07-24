var width,height;
var line;
var count = 0;
var motions = new Array();
var molo = new Image();
var prevMotion = null;
var paper = null;

for(var i=0;i<80;i++){
	motions.push(Math.round(40*Math.sin(i/40*Math.PI)));
}

$(window).resize(function(event){
	width = $(document.body).width();
	if(paper != null){
		paper.setSize(width,height);
	}
	$('#molo-image').css('left', Math.floor((width - molo.width)*0.5));
});

$(document).ready(function(){
	molo.src = "/img/404/molome_404.png";
	$('#molo').hide();
	
	width = window.innerWidth;//parseInt($(document.body).css('width').replace("px",""));
	height = 300;
	var imageWidth = parseInt($('#molo-image').css('width').replace("px",""));
	paper = Raphael(document.getElementById("rope-anim"), width, height);
	
	$("#1").lavaLamp({
        fx: "backout",
        speed: 700,
        click: function(event, menuItem) {
            return true;
        }
    });
	
	$('#error').css('height',$(document).height()-parseInt($('#error').css('padding-top').replace("px",""))-180+"px");
	
	$(document.body).css('overflow','hidden');
	
	
	var frontWidth = 250;
	var cloudFront = function(){
	$('#cloud-front').css('left',width+frontWidth);
		$('#cloud-front').animate({
			left: -frontWidth
		},12000,"linear",function(){
			setTimeout(cloudFront,100);
		});
	};
	cloudFront();
	
	var backWidth = 123;
	var cloudBack = function(){
	$('#cloud-back').css('left',width+backWidth);
		$('#cloud-back').animate({
			left: -frontWidth
		},24000,"linear",function(){
			setTimeout(cloudBack,100);
		});
	};
	cloudBack();
	
	var callback = function(){
		if(count != 0 && motions[count-1] == motions[count]){
			setTimeout(callback,20);
		}else{
			
			if(line != null){
				line.remove();
			}
			
			$('#molo').animate({marginTop:motions[count]},20,"linear",callback);
			
			line = paper.path("M0 80S"+Math.round(width/2)+" "+(prevMotion*2.325+135)+" "+width+" 80");
			line.attr("stroke", "#BBBBBB");
			line.attr("stroke-width", "3");
			//line.attr("stroke-opacity", "0.5");
			
			
		}
		
		prevMotion = motions[count];
		count = (count+1) % motions.length;
	};
	
	if (molo.complete){
		$('#molo').attr('src',molo.src);
		$('#molo').show();
		$('#molo-image').css('left', Math.floor((width - molo.width)*0.5));
		callback();
	}else{        
		molo.onload = function(){
			$('#molo').attr('src',molo.src);
			$('#molo').show();
			$('#molo-image').css('left', Math.floor((width - molo.width)*0.5));
			callback();
		};
	}
	
});