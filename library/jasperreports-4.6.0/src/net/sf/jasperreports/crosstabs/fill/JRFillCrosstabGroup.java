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
package net.sf.jasperreports.crosstabs.fill;

import net.sf.jasperreports.crosstabs.JRCellContents;
import net.sf.jasperreports.crosstabs.JRCrosstabBucket;
import net.sf.jasperreports.crosstabs.JRCrosstabGroup;
import net.sf.jasperreports.crosstabs.type.CrosstabTotalPositionEnum;
import net.sf.jasperreports.engine.JRVariable;
import net.sf.jasperreports.engine.fill.JRFillCellContents;
import net.sf.jasperreports.engine.fill.JRFillVariable;

/**
 * Base crosstab row/column group implementation used at fill time. 
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRFillCrosstabGroup.java 5180 2012-03-29 13:23:12Z teodord $
 */
public abstract class JRFillCrosstabGroup implements JRCrosstabGroup
{
	protected JRCrosstabGroup parentGroup;
	protected JRFillCellContents header;
	protected JRFillCellContents totalHeader;
	protected JRFillVariable variable;

	public JRFillCrosstabGroup(JRCrosstabGroup group, String cellType, JRFillCrosstabObjectFactory factory)
	{
		factory.put(group, this);
		
		parentGroup = group;
		
		header = factory.getCell(group.getHeader(), cellType);
		totalHeader = factory.getCell(group.getTotalHeader(), cellType);
		
		variable = factory.getVariable(group.getVariable());
	}

	public String getName()
	{
		return parentGroup.getName();
	}

	public CrosstabTotalPositionEnum getTotalPositionValue()
	{
		return parentGroup.getTotalPositionValue();
	}

	public boolean hasTotal()
	{
		return parentGroup.hasTotal();
	}

	public JRCrosstabBucket getBucket()
	{
		return parentGroup.getBucket();
	}

	public JRCellContents getHeader()
	{
		return header;
	}

	public JRCellContents getTotalHeader()
	{
		return totalHeader;
	}

	public JRFillCellContents getFillHeader()
	{
		return header;
	}

	public JRFillCellContents getFillTotalHeader()
	{
		return totalHeader;
	}
	
	public JRVariable getVariable()
	{
		return variable;
	}
	
	public JRFillVariable getFillVariable()
	{
		return variable;
	}
	
	/**
	 *
	 */
	public Object clone() 
	{
		throw new UnsupportedOperationException();
	}
}
