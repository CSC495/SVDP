/*
 * JasperReports - Free Java Reporting Library.
 * Copyright (C) 2001 - 2011 Jaspersoft Corporation. All rights reserved.
 * http://www.jaspersoft.com
 *
 * Unless you have purchased a commercial license agreement from Jaspersoft,
 * the following license terms apply:
 *
 * This program is part of JasperReports.
 *
 * JasperReports is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JasperReports is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with JasperReports. If not, see <http://www.gnu.org/licenses/>.
 */
package net.sf.jasperreports.engine.type;

/**
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: JsonOperatorEnum.java 4845 2011-12-12 14:08:10Z narcism $
 */
public enum JsonOperatorEnum {
	
	LT("<", "Lower than"),
	LE("<=", "Lower or equal"),
	GT(">", "Greater than"),
	GE(">=", "Greater or equal"),
	EQ("==", "Equal"),
	NE("!=", "Not equal");

	private final transient String value;
	private final transient String name;

	private JsonOperatorEnum(String value, String name) {
		this.value = value;
		this.name = name;
	}

	/**
	 *
	 */
	public final String getValue() {
		return value;
	}
	
	/**
	 *
	 */
	public String getName() {
		return name;
	}
	
	/**
	 * 
	 */
	public static JsonOperatorEnum getByValue(String value) {
		for (JsonOperatorEnum joe: values()) {
			if (value.equals(joe.getValue())) {
				return joe;
			}
		}
		return null;
	}
	
}