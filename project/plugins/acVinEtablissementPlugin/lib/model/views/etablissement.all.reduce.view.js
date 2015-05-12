function (keys, values, rereduce) {
    
	if(!rereduce) {
		return values.length;
	}

	return sum(values);
}
