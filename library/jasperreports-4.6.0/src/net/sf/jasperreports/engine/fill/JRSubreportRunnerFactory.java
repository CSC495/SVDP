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
package net.sf.jasperreports.engine.fill;

import net.sf.jasperreports.engine.JRPropertiesUtil;


/**
 * Factory of {@link net.sf.jasperreports.engine.fill.JRSubreportRunner JRSubreportRunner} instances.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRSubreportRunnerFactory.java 5180 2012-03-29 13:23:12Z teodord $
 */
public interface JRSubreportRunnerFactory
{
	/**
	 * Property specifying the {@link net.sf.jasperreports.engine.fill.JRSubreportRunnerFactory JRSubreportRunnerFactory}
	 * implementation to use for creating subreport runners.
	 */
	public static final String SUBREPORT_RUNNER_FACTORY = JRPropertiesUtil.PROPERTY_PREFIX + "subreport.runner.factory";
	
	/**
	 * Creates a new {@link net.sf.jasperreports.engine.fill.JRSubreportRunner JRSubreportRunner} instance.
	 * 
	 * @param fillSubreport the subreport element of the master report
	 * @param subreportFiller the subreport filler created to fill the subreport
	 * @return a new {@link net.sf.jasperreports.engine.fill.JRSubreportRunner JRSubreportRunner} instance
	 */
	JRSubreportRunner createSubreportRunner(JRFillSubreport fillSubreport, JRBaseFiller subreportFiller);
}
