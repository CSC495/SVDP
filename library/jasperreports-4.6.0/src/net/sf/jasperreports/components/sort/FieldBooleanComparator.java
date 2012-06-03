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
package net.sf.jasperreports.components.sort;


/**
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: FieldBooleanComparator.java 5257 2012-04-10 16:14:03Z narcism $
 */
public class FieldBooleanComparator extends AbstractFieldComparator<Boolean> {

	public FieldBooleanComparator() {
	}
	
	@Override
	public void initValues() throws Exception {
	}
	
	@Override
	public boolean compare(String filterTypeOperator) {
		boolean result = true;
		FilterTypeBooleanOperatorsEnum booleanEnum = FilterTypeBooleanOperatorsEnum.getByEnumConstantName(filterTypeOperator);

		switch (booleanEnum) {
			case IS_TRUE:
				result = Boolean.TRUE.equals(compareTo);
				break;
			case IS_NOT_TRUE:
				result = !Boolean.TRUE.equals(compareTo);
				break;
			case IS_FALSE:
				result = Boolean.FALSE.equals(compareTo);
				break;
			case IS_NOT_FALSE:
				result = !Boolean.FALSE.equals(compareTo);
				break;
		}
		
		return result;
	}

}
