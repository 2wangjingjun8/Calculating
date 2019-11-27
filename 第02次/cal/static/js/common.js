function clearForm(formObj){var allElements = formObj.elements; for (i = 0; i < allElements.length; i++){if ( allElements[i].type == 'text'||allElements[i].type == 'number' ) allElements[i].value= '';}}

function IsPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone",
                "SymbianOS", "Windows Phone",
                "iPad", "iPod"];
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}

function printDeal(){
	var printBox = document.getElementById('printBox');
	//拿到打印的区域的html内容
	var newContent =printBox.innerHTML;
	//将旧的页面储存起来，当打印完成后返给给页面。
	var oldContent = document.body.innerHTML;
	//赋值给body
	document.body.innerHTML = newContent;
	//执行window.print打印功能
	window.print();
	// 重新加载页面，以刷新数据。以防打印完之后，页面不能操作的问题
	window.location.reload();
	document.body.innerHTML = oldContent;
	return false;
}
function printDeal1(){
	var printBox = document.getElementById('printBox1');
	//拿到打印的区域的html内容
	var newContent =printBox.innerHTML;
	//将旧的页面储存起来，当打印完成后返给给页面。
	var oldContent = document.body.innerHTML;
	//赋值给body
	document.body.innerHTML = newContent;
	//执行window.print打印功能
	window.print();
	// 重新加载页面，以刷新数据。以防打印完之后，页面不能操作的问题
	window.location.reload();
	document.body.innerHTML = oldContent;
	return false;
}

function toggleLook(){
	$('.pc-table').toggle();
	$('.mobile-table').toggle()
}