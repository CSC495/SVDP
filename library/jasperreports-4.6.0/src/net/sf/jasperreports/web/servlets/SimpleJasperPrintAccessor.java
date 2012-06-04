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
package net.sf.jasperreports.web.servlets;

import java.io.Serializable;

import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JasperPrint;

/**
 * Generated report accessor used for fully generated reports.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: SimpleJasperPrintAccessor.java 5293 2012-04-24 15:48:15Z lucianc $
 */
public class SimpleJasperPrintAccessor implements JasperPrintAccessor, Serializable
{

	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;

	private final JasperPrint jasperPrint;
	
	/**
	 * Create a report accessor.
	 * 
	 * @param jasperPrint the generated report
	 */
	public SimpleJasperPrintAccessor(JasperPrint jasperPrint)
	{
		this.jasperPrint = jasperPrint;
	}

	public ReportPageStatus pageStatus(int pageIdx, Long pageTimestamp)
	{
		return pageIdx < jasperPrint.getPages().size() ? ReportPageStatus.PAGE_FINAL : ReportPageStatus.NO_SUCH_PAGE;
	}

	public JasperPrint getJasperPrint()
	{
		return jasperPrint;
	}

	public JasperPrint getFinalJasperPrint()
	{
		return jasperPrint;
	}

	@Override
	public ReportExecutionStatus getReportStatus()
	{
		return ReportExecutionStatus.finished(jasperPrint.getPages().size());
	}

}
