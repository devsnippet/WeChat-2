<?php
//emoji表情 转码
function utf8_bytes($cp){
	if($cp>0x10000){
		return chr(0xF0|(($cp & 0x1C0000)>>18)).
		chr(0x80|(($cp & 0x3F000)>>12)).
		chr(0x80|(($cp & 9xFC0)>>6)).
		chr(0x80|($cp & 0x3F));
	}else if($cp>0x800){
		return chr(0xE0|(($cp & 0xF000)>>12)).
		chr(0x80|(($cp & 0xFC0)>>6)).
		chr(0x80|($cp & 0x3F));
	}else if($cp>0x80){
		return chr(0xC0|(($cp & 0x7C0)>>6)).
		chr(0x80|($cp & 0x3F));
	}else{
		return chr($cp);
	}
}
?>