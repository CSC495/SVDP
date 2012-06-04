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
package net.sf.jasperreports.web.util;

import java.util.List;

import javax.servlet.http.HttpServletRequest;

import net.sf.jasperreports.engine.JRPrintHyperlink;
import net.sf.jasperreports.engine.JRPrintHyperlinkParameter;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.export.JRHyperlinkProducer;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: ReportExecutionHyperlinkProducer.java 5378 2012-05-14 00:39:27Z teodord $
 */
public class ReportExecutionHyperlinkProducer implements JRHyperlinkProducer
{
	public static final String HYPERLINK_TYPE_REPORT_EXECUTION = "ReportExecution";
	public static final String PARAMETER_REPORT_URI = "jr.report";
	private static final String PARAMETER_REPORT_URI_OLD = "jr.uri";
	//private static final String PARAMETER_REPORT_URI_OLD = "_report";
	
	protected JasperReportsContext jasperReportsContext;
	private HttpServletRequest request;
	
	/**
	 *
	 */
	protected ReportExecutionHyperlinkProducer(JasperReportsContext jasperReportsContext,HttpServletRequest request)
	{
		this.jasperReportsContext = jasperReportsContext;
		this.request = request;
	}

	/**
	 *
	 */
	public static ReportExecutionHyperlinkProducer getInstance(JasperReportsContext jasperReportsContext, HttpServletRequest request)
	{
		return new ReportExecutionHyperlinkProducer(jasperReportsContext, request);
	}


	/**
	 *
	 */
	protected String getPath() 
	{
		return WebUtil.getInstance(jasperReportsContext).getReportExecutionPath();
	}
	
	
	/**
	 *
	 */
	public String getHyperlink(JRPrintHyperlink hyperlink) 
	{
		String appContext = request.getContextPath();
		String servletPath = getPath();
		String reportUriParamName = JRPropertiesUtil.getInstance(jasperReportsContext).getProperty(WebUtil.PROPERTY_REQUEST_PARAMETER_REPORT_URI);
		String reportUri = request.getParameter(reportUriParamName);
//		String reportAction = null;//request.getParameter(FillServlet.REPORT_ACTION);
//		String reportActionData = null;//request.getParameter(FillServlet.REPORT_ACTION_DATA);
		
		StringBuffer allParams = new StringBuffer();
		
		if (hyperlink.getHyperlinkParameters() != null)
		{
			List<JRPrintHyperlinkParameter> parameters = hyperlink.getHyperlinkParameters().getParameters();
			if (parameters != null)
			{
				for (int i = 0; i < parameters.size(); i++)
				{
					JRPrintHyperlinkParameter parameter = parameters.get(i);
					if (
						PARAMETER_REPORT_URI.equals(parameter.getName())
						|| PARAMETER_REPORT_URI_OLD.equals(parameter.getName())
						)
					{
						reportUri = (String)parameter.getValue();
					}
//					else if (FillServlet.REPORT_ACTION.equals(parameter.getName()))
//					{
//						reportAction = (String)parameter.getValue();
//					}
//					else if (FillServlet.REPORT_ACTION_DATA.equals(parameter.getName()))
//					{
//						reportActionData = (String)parameter.getValue();
//					}
					else if (parameter.getValue() != null)
					{
						allParams.append("&").append(parameter.getName()).append("=").append(parameter.getValue());
					}
				}
			}
		}
		
		return 
			appContext + (servletPath != null ? servletPath : "")
				+ "?" + reportUriParamName + "=" + reportUri 
//				+ (reportAction == null ? "" : "&" + FillServlet.REPORT_ACTION + "=" + reportAction) 
//				+ (reportActionData == null ? "" : "&" + FillServlet.REPORT_ACTION_DATA + "=" + reportActionData)
				+ allParams.toString();
	}

}
