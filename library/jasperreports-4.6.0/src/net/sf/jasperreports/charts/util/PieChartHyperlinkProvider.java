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
package net.sf.jasperreports.charts.util;

import java.util.Map;

import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRPrintHyperlink;

import org.jfree.chart.entity.ChartEntity;
import org.jfree.chart.entity.PieSectionEntity;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: PieChartHyperlinkProvider.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class PieChartHyperlinkProvider implements ChartHyperlinkProvider
{
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	
	private Map<Comparable<?>, JRPrintHyperlink> sectionHyperlinks;
	
	public PieChartHyperlinkProvider(Map<Comparable<?>, JRPrintHyperlink> sectionHyperlinks)
	{
		this.sectionHyperlinks = sectionHyperlinks;
	}

	
	public JRPrintHyperlink getEntityHyperlink(ChartEntity entity)
	{
		JRPrintHyperlink printHyperlink = null;
		if (hasHyperlinks() && entity instanceof PieSectionEntity)
		{
			PieSectionEntity pieEntity = (PieSectionEntity) entity;
			printHyperlink = sectionHyperlinks.get(pieEntity.getSectionKey());
		}
		return printHyperlink;
	}

	public boolean hasHyperlinks()
	{
		return sectionHyperlinks != null && sectionHyperlinks.size() > 0;
	}
}
