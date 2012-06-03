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
package net.sf.jasperreports.web.commands;

import java.util.List;

import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.ReportContext;
import net.sf.jasperreports.web.actions.AbstractAction;
import net.sf.jasperreports.web.actions.ActionException;


/**
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: MultiActionCommand.java 5298 2012-04-25 14:33:37Z narcism $
 */
public class MultiActionCommand implements Command {
	
	private JasperReportsContext jasperReportsContext;
	private ReportContext reportContext;
	private List<AbstractAction> actions;
	private CommandStack individualResizeCommandStack;
	
	public MultiActionCommand(List<AbstractAction> actions, JasperReportsContext jasperReportsContext, ReportContext reportContext) {
		this.actions = actions;
		this.jasperReportsContext = jasperReportsContext;
		this.reportContext = reportContext;
		this.individualResizeCommandStack = new CommandStack();
	}

	@Override
	public void execute() throws CommandException {
		if (actions != null) {
			for (AbstractAction action: actions) {
				action.init(jasperReportsContext, reportContext);
				action.setCommandStack(individualResizeCommandStack);
				try {
					action.run();
				} catch (ActionException e) {
					throw new CommandException(e.getMessage());
				}
			}
		}
	}

	@Override
	public void undo() {
		individualResizeCommandStack.undoAll();
	}

	@Override
	public void redo() {
		individualResizeCommandStack.redoAll();
	}

}
