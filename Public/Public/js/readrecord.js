var read;
if($.cookie("user_id")){
	if ($.cookie("lbname")) {
	    read = "<a href=\"/Bookcase/reading\"><div class=\"l\"><h2>"+$.cookie("lbname")+"</h2><p style=\"color:grey;\">"+$.cookie("lcname")+"</p></div></a>";
	    // read += "<div class=\"r\" style=\"margin-right: 2%;\"><h1>&gt;</h1></div>";
	    document.write(read);
	} else {
	    read = "<div class=\"l\"><h3>"+"暂无"+"</h3></div>";
	    document.write(read);
	}
}else{
	 read = "<div class=\"l\"><h3>"+"暂无"+"</h3></div>";
	 document.write(read);
}